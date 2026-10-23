<?php

namespace Tests\Feature;

use App\Services\YandexGptClient;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class YandexGptClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ai.yandexgpt.url' => 'https://yandex.test/v1/responses',
            'ai.yandexgpt.api_key' => 'test-key',
            'ai.yandexgpt.folder_id' => 'test-folder',
            'ai.yandexgpt.model' => 'gpt-oss-120b/latest',
            'ai.yandexgpt.temperature' => 0.7,
            'ai.yandexgpt.max_output_tokens' => 5000,
            'ai.yandexgpt.timeout' => 30,
            'ai.yandexgpt.connect_timeout' => 5,
        ]);
    }

    public function test_it_returns_the_answer_and_builds_the_model_uri(): void
    {
        Http::fake([
            'https://yandex.test/v1/responses' => Http::response([
                'output_text' => 'Ответ YandexGPT',
                'usage' => ['total_tokens' => 42],
            ]),
        ]);

        $result = app(YandexGptClient::class)->ask('Вопрос');

        $this->assertSame('Ответ YandexGPT', $result['answer']);
        $this->assertSame('gpt-oss-120b/latest', $result['model']);
        $this->assertSame(['total_tokens' => 42], $result['usage']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://yandex.test/v1/responses'
                && $request->hasHeader('Authorization', 'Api-Key test-key')
                && $request['model'] === 'gpt://test-folder/gpt-oss-120b/latest'
                && $request['input'] === 'Вопрос';
        });
    }

    public function test_it_throws_when_credentials_are_missing(): void
    {
        config([
            'ai.yandexgpt.api_key' => null,
            'ai.yandexgpt.folder_id' => null,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('YANDEX_GPT_API_KEY or YANDEX_GPT_FOLDER_ID is not configured');

        app(YandexGptClient::class)->ask('Вопрос');
    }

    public function test_it_throws_on_http_error(): void
    {
        Http::fake([
            'https://yandex.test/v1/responses' => Http::response([
                'error' => ['message' => 'quota exceeded'],
            ], 429),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('HTTP 429 quota exceeded');

        app(YandexGptClient::class)->ask('Вопрос');
    }
}
