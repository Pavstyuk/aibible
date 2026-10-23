<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class YandexGptClient
{
    /**
     * Send a prompt to YandexGPT and return the generated text.
     *
     * @return array{answer: string, model: string, usage: array<string, mixed>|null, http_code: int}
     */
    public function ask(string $prompt): array
    {
        $apiKey = trim((string) config('ai.yandexgpt.api_key', ''));
        $folderId = trim((string) config('ai.yandexgpt.folder_id', ''));

        if ($apiKey === '' || $folderId === '') {
            throw new RuntimeException('YANDEX_GPT_API_KEY or YANDEX_GPT_FOLDER_ID is not configured.');
        }

        $configuredModel = trim((string) config('ai.yandexgpt.model', 'gpt-oss-120b/latest'));
        $modelUri = str_starts_with($configuredModel, 'gpt://')
            ? $configuredModel
            : 'gpt://' . $folderId . '/' . ltrim($configuredModel, '/');
        $displayModel = preg_replace('#^gpt://[^/]+/#', '', $modelUri) ?: $configuredModel;

        $payload = [
            'model' => $modelUri,
            'instructions' => '',
            'input' => $prompt,
            'temperature' => (float) config('ai.yandexgpt.temperature', 0.7),
            'max_output_tokens' => (int) config('ai.yandexgpt.max_output_tokens', 5000),
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Api-Key ' . $apiKey,
            'OpenAI-Project' => $folderId,
        ])
            ->connectTimeout((int) config('ai.yandexgpt.connect_timeout', 10))
            ->timeout((int) config('ai.yandexgpt.timeout', 90))
            ->post((string) config('ai.yandexgpt.url', 'https://ai.api.cloud.yandex.net/v1/responses'), $payload);

        $body = $response->json();
        $body = is_array($body) ? $body : [];

        if (! $response->successful()) {
            $error = data_get($body, 'error.message');

            throw new RuntimeException(
                'YandexGPT request failed: HTTP ' . $response->status()
                . (is_string($error) && $error !== '' ? ' ' . $error : ' ' . Str::limit($response->body(), 1000, ''))
            );
        }

        $answer = $this->extractAnswer($body);

        if ($answer === '') {
            throw new RuntimeException('YandexGPT returned an empty response.');
        }

        return [
            'answer' => $answer,
            'model' => $displayModel,
            'usage' => is_array($body['usage'] ?? null) ? $body['usage'] : null,
            'http_code' => $response->status(),
        ];
    }

    private function extractAnswer(array $body): string
    {
        $outputText = $body['output_text'] ?? null;

        if (is_string($outputText) && trim($outputText) !== '') {
            return trim($outputText);
        }

        $parts = [];

        foreach ((array) ($body['output'] ?? []) as $output) {
            foreach ((array) data_get($output, 'content', []) as $content) {
                $type = data_get($content, 'type');
                $text = data_get($content, 'text');

                if (($type === null || $type === 'output_text') && is_string($text) && trim($text) !== '') {
                    $parts[] = trim($text);
                }
            }
        }

        return trim(implode("\n\n", $parts));
    }
}
