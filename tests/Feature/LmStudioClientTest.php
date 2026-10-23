<?php

namespace Tests\Feature;

use App\Services\LmStudioClient;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class LmStudioClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ai.lmstudio.url' => 'http://lm.test/v1/chat/completions',
            'ai.lmstudio.model' => 'test-model',
            'ai.lmstudio.api_key' => null,
            'ai.lmstudio.temperature' => 0.7,
            'ai.lmstudio.max_output_tokens' => 2000,
            'ai.lmstudio.reasoning' => 'off',
            'ai.lmstudio.timeout' => 30,
            'ai.lmstudio.connect_timeout' => 5,
        ]);
    }

    public function test_it_handles_the_openai_compatible_api(): void
    {
        Http::fake([
            'http://lm.test/v1/chat/completions' => Http::response([
                'model' => 'lm-model',
                'choices' => [
                    ['message' => ['content' => 'Ответ LM Studio']],
                ],
                'usage' => ['total_tokens' => 12],
            ]),
        ]);

        $result = app(LmStudioClient::class)->ask('Вопрос');

        $this->assertSame('Ответ LM Studio', $result['answer']);
        $this->assertSame('lm-model', $result['model']);
        $this->assertSame(['total_tokens' => 12], $result['usage']);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://lm.test/v1/chat/completions'
                && $request['messages'][0]['content'] === 'Вопрос'
                && $request['max_tokens'] === 2000;
        });
    }

    public function test_it_handles_the_native_api(): void
    {
        config(['ai.lmstudio.url' => 'http://lm.test/api/v1/chat']);

        Http::fake([
            'http://lm.test/api/v1/chat' => Http::response([
                'model_instance_id' => 'native-model',
                'output' => [
                    ['type' => 'message', 'content' => 'Ответ native API'],
                ],
                'stats' => ['total_tokens' => 15],
            ]),
        ]);

        $result = app(LmStudioClient::class)->ask('Вопрос');

        $this->assertSame('Ответ native API', $result['answer']);
        $this->assertSame('native-model', $result['model']);
        $this->assertSame(['total_tokens' => 15], $result['usage']);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://lm.test/api/v1/chat'
                && $request['input'] === 'Вопрос'
                && $request['max_output_tokens'] === 2000
                && $request['reasoning'] === 'off';
        });
    }

    public function test_it_throws_on_http_error(): void
    {
        Http::fake([
            'http://lm.test/v1/chat/completions' => Http::response([
                'error' => 'model not loaded',
            ], 503),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('HTTP 503 model not loaded');

        app(LmStudioClient::class)->ask('Вопрос');
    }
}
