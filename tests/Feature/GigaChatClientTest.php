<?php

namespace Tests\Feature;

use App\Services\GigaChatClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class GigaChatClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        config([
            'ai.gigachat.oauth_url' => 'https://oauth.test/token',
            'ai.gigachat.chat_url' => 'https://chat.test/v1/chat/completions',
            'ai.gigachat.auth_key' => base64_encode('client-id:client-secret'),
            'ai.gigachat.client_id' => null,
            'ai.gigachat.client_secret' => null,
            'ai.gigachat.scope' => 'GIGACHAT_API_PERS',
            'ai.gigachat.model' => 'GigaChat-3-Ultra',
            'ai.gigachat.temperature' => 0.7,
            'ai.gigachat.max_tokens' => 2000,
            'ai.gigachat.ca_bundle' => null,
        ]);
    }

    public function test_it_requests_a_token_and_returns_the_chat_answer(): void
    {
        Http::fake([
            'https://oauth.test/token' => Http::response([
                'access_token' => 'token-1',
                'expires_at' => (time() + 1800) * 1000,
            ]),
            'https://chat.test/v1/chat/completions' => Http::response([
                'model' => 'GigaChat-3-Ultra',
                'choices' => [
                    ['message' => ['content' => 'Ответ GigaChat']],
                ],
                'usage' => ['total_tokens' => 42],
            ]),
        ]);

        $result = app(GigaChatClient::class)->ask('Вопрос');

        $this->assertSame('Ответ GigaChat', $result['answer']);
        $this->assertSame('GigaChat-3-Ultra', $result['model']);
        $this->assertSame('token-1', Cache::get('gigachat_access_token'));

        Http::assertSent(function ($request) {
            return $request->url() === 'https://oauth.test/token'
                && $request->hasHeader('Authorization', 'Basic ' . base64_encode('client-id:client-secret'))
                && $request['scope'] === 'GIGACHAT_API_PERS';
        });

        Http::assertSent(function ($request) {
            return $request->url() === 'https://chat.test/v1/chat/completions'
                && $request->hasHeader('Authorization', 'Bearer token-1')
                && $request['model'] === 'GigaChat-3-Ultra'
                && $request['messages'][0]['content'] === 'Вопрос';
        });
    }

    public function test_it_accepts_the_legacy_tok_and_exp_token_fields(): void
    {
        Http::fake([
            'https://oauth.test/token' => Http::response([
                'tok' => 'legacy-token',
                'exp' => time() + 1800,
            ]),
            'https://chat.test/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Ответ со старым форматом токена']],
                ],
            ]),
        ]);

        $result = app(GigaChatClient::class)->ask('Вопрос');

        $this->assertSame('Ответ со старым форматом токена', $result['answer']);
        $this->assertSame('legacy-token', Cache::get('gigachat_access_token'));

        Http::assertSent(function ($request) {
            return $request->url() === 'https://chat.test/v1/chat/completions'
                && $request->hasHeader('Authorization', 'Bearer legacy-token');
        });
    }

    public function test_it_refreshes_the_token_once_after_a_401_response(): void
    {
        $oauthRequests = 0;

        Http::fake(function ($request) use (&$oauthRequests) {
            if ($request->url() === 'https://oauth.test/token') {
                $oauthRequests++;

                return Http::response([
                    'access_token' => 'token-' . $oauthRequests,
                    'expires_at' => (time() + 1800) * 1000,
                ]);
            }

            if ($request->url() === 'https://chat.test/v1/chat/completions') {
                return $request->hasHeader('Authorization', 'Bearer token-2')
                    ? Http::response([
                        'choices' => [
                            ['message' => ['content' => 'Ответ после обновления']],
                        ],
                    ])
                    : Http::response(['error' => 'unauthorized'], 401);
            }

            return Http::response('not found', 404);
        });

        $result = app(GigaChatClient::class)->ask('Вопрос');

        $this->assertSame('Ответ после обновления', $result['answer']);
        $this->assertSame(2, $oauthRequests);
        $this->assertSame('token-2', Cache::get('gigachat_access_token'));
    }

    public function test_it_throws_when_credentials_are_missing(): void
    {
        config([
            'ai.gigachat.auth_key' => null,
            'ai.gigachat.client_id' => null,
            'ai.gigachat.client_secret' => null,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('GigaChat credentials are not configured');

        app(GigaChatClient::class)->accessToken();
    }
}
