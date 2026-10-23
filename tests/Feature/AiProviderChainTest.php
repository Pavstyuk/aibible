<?php

namespace Tests\Feature;

use App\Exceptions\AiProvidersFailedException;
use App\Services\AiProviderChain;
use App\Services\GigaChatClient;
use App\Services\LmStudioClient;
use App\Services\YandexGptClient;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class AiProviderChainTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_it_falls_back_in_order_until_a_provider_succeeds(): void
    {
        $gigaChat = Mockery::mock(GigaChatClient::class);
        $gigaChat->shouldReceive('ask')->once()->with('Вопрос')->andThrow(new RuntimeException('GigaChat unavailable'));

        $yandexGpt = Mockery::mock(YandexGptClient::class);
        $yandexGpt->shouldReceive('ask')->once()->with('Вопрос')->andReturn([
            'answer' => 'Ответ YandexGPT',
            'model' => 'gpt-oss-120b/latest',
            'usage' => null,
            'http_code' => 200,
        ]);

        $lmStudio = Mockery::mock(LmStudioClient::class);
        $lmStudio->shouldNotReceive('ask');

        $chain = new AiProviderChain($gigaChat, $yandexGpt, $lmStudio);
        $result = $chain->ask('Вопрос');

        $this->assertSame('Ответ YandexGPT', $result['answer']);
        $this->assertSame('yandexgpt', $result['provider']);
        $this->assertSame(['gigachat' => 'GigaChat unavailable'], $result['fallback_errors']);
    }

    public function test_it_throws_when_all_providers_fail(): void
    {
        $gigaChat = Mockery::mock(GigaChatClient::class);
        $gigaChat->shouldReceive('ask')->once()->andThrow(new RuntimeException('gigachat down'));

        $yandexGpt = Mockery::mock(YandexGptClient::class);
        $yandexGpt->shouldReceive('ask')->once()->andThrow(new RuntimeException('yandex down'));

        $lmStudio = Mockery::mock(LmStudioClient::class);
        $lmStudio->shouldReceive('ask')->once()->andThrow(new RuntimeException('lm studio down'));

        $chain = new AiProviderChain($gigaChat, $yandexGpt, $lmStudio);

        try {
            $chain->ask('Вопрос');
            $this->fail('Expected AiProvidersFailedException.');
        } catch (AiProvidersFailedException $exception) {
            $this->assertSame([
                'gigachat' => 'gigachat down',
                'yandexgpt' => 'yandex down',
                'lmstudio' => 'lm studio down',
            ], $exception->errors);
        }
    }
}
