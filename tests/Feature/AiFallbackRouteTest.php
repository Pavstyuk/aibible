<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AiProviderChain;
use App\Services\GigaChatClient;
use App\Services\LmStudioClient;
use App\Services\YandexGptClient;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery\MockInterface;
use Tests\TestCase;

class AiFallbackRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['ai.email_notifications' => false]);

        Schema::dropIfExists('rbo');
        Schema::create('rbo', function (Blueprint $table) {
            $table->id();
        });

        DB::table('rbo')->insert(['id' => 1]);

        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $user->id = 1;

        $this->actingAs($user);
    }

    public function test_ask_ai_uses_the_fallback_chain(): void
    {
        $this->mock(AiProviderChain::class, function (MockInterface $mock) {
            $mock->shouldReceive('ask')
                ->once()
                ->withArgs(fn (string $prompt) => str_contains($prompt, 'Ин 3:16'))
                ->andReturn($this->providerResult('Ответ из fallback-цепочки', 'GigaChat-3-Ultra', 'gigachat'));
        });

        $response = $this->postPrompt('ask-ai');

        $response->assertOk();
        $response->assertSee('Ответ из fallback-цепочки');
        $response->assertSee('data-model="GigaChat-3-Ultra"', false);
    }

    public function test_ask_giga_uses_only_gigachat(): void
    {
        $this->mock(GigaChatClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('ask')
                ->once()
                ->withArgs(fn (string $prompt) => str_contains($prompt, 'Ин 3:16'))
                ->andReturn($this->providerResult('Ответ GigaChat', 'GigaChat-3-Ultra'));
        });

        $response = $this->postPrompt('ask-giga');

        $response->assertOk();
        $response->assertSee('Ответ GigaChat');
        $response->assertSee('data-model="GigaChat-3-Ultra"', false);
    }

    public function test_ask_yandex_uses_only_yandexgpt(): void
    {
        $this->mock(YandexGptClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('ask')
                ->once()
                ->withArgs(fn (string $prompt) => str_contains($prompt, 'Ин 3:16'))
                ->andReturn($this->providerResult('Ответ YandexGPT', 'gpt-oss-120b/latest'));
        });

        $response = $this->postPrompt('ask-yandex');

        $response->assertOk();
        $response->assertSee('Ответ YandexGPT');
        $response->assertSee('data-model="gpt-oss-120b/latest"', false);
    }

    public function test_ask_local_uses_only_lm_studio(): void
    {
        $this->mock(LmStudioClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('ask')
                ->once()
                ->withArgs(fn (string $prompt) => str_contains($prompt, 'Ин 3:16'))
                ->andReturn($this->providerResult('Ответ LM Studio', 'google/gemma-4-12b-qat'));
        });

        $response = $this->postPrompt('ask-local');

        $response->assertOk();
        $response->assertSee('Ответ LM Studio');
        $response->assertSee('data-model="google/gemma-4-12b-qat"', false);
    }

    /**
     * @return array{answer: string, model: string, usage: null, http_code: int, provider: string, fallback_errors: array<string, string>}
     */
    private function providerResult(string $answer, string $model, string $provider = 'gigachat'): array
    {
        return [
            'answer' => $answer,
            'model' => $model,
            'usage' => null,
            'http_code' => 200,
            'provider' => $provider,
            'fallback_errors' => [],
        ];
    }

    private function postPrompt(string $route)
    {
        return $this->post('/htmx/' . $route, [
            'verse' => urlencode('Ин 3:16'),
            'verse_id' => 1,
            'translation' => 'rbo',
            'type' => 'comment',
        ]);
    }
}
