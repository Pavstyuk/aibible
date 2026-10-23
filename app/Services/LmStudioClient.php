<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class LmStudioClient
{
    /**
     * Send a prompt to LM Studio and return the generated text.
     *
     * @return array{answer: string, model: string, usage: array<string, mixed>|null, http_code: int}
     */
    public function ask(string $prompt): array
    {
        $url = (string) config('ai.lmstudio.url');
        $model = (string) config('ai.lmstudio.model');
        $nativeApi = str_contains((string) parse_url($url, PHP_URL_PATH), '/api/v1/chat');
        $temperature = (float) config('ai.lmstudio.temperature', 0.7);
        $maxOutputTokens = (int) config('ai.lmstudio.max_output_tokens', 2000);

        if ($nativeApi) {
            $payload = [
                'model' => $model,
                'input' => $prompt,
                'temperature' => $temperature,
                'stream' => false,
                'max_output_tokens' => $maxOutputTokens,
            ];

            $reasoning = config('ai.lmstudio.reasoning', 'off');

            if (is_string($reasoning) && $reasoning !== '') {
                $payload['reasoning'] = $reasoning;
            }
        } else {
            $payload = [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => $temperature,
                'stream' => false,
                'max_tokens' => $maxOutputTokens,
            ];
        }

        $headers = [];
        $apiKey = trim((string) config('ai.lmstudio.api_key', ''));

        if ($apiKey !== '') {
            $headers['Authorization'] = 'Bearer ' . $apiKey;
        }

        $response = Http::withHeaders($headers)
            ->acceptJson()
            ->connectTimeout((int) config('ai.lmstudio.connect_timeout', 10))
            ->timeout((int) config('ai.lmstudio.timeout', 180))
            ->post($url, $payload);

        $body = $response->json();
        $body = is_array($body) ? $body : [];

        if (! $response->successful() || isset($body['error'])) {
            $error = $body['error'] ?? null;

            throw new RuntimeException(
                'LM Studio request failed: HTTP ' . $response->status()
                . (is_string($error) && $error !== '' ? ' ' . $error : ' ' . Str::limit($response->body(), 1000, ''))
            );
        }

        $answer = $nativeApi
            ? $this->extractNativeAnswer($body)
            : (string) data_get($body, 'choices.0.message.content', '');

        if (trim($answer) === '') {
            throw new RuntimeException('LM Studio returned an empty response.');
        }

        return [
            'answer' => trim($answer),
            'model' => (string) ($body['model_instance_id'] ?? $body['model'] ?? $model),
            'usage' => is_array($body['usage'] ?? null)
                ? $body['usage']
                : (is_array($body['stats'] ?? null) ? $body['stats'] : null),
            'http_code' => $response->status(),
        ];
    }

    private function extractNativeAnswer(array $body): string
    {
        $parts = [];

        foreach ((array) ($body['output'] ?? []) as $item) {
            if (($item['type'] ?? null) !== 'message') {
                continue;
            }

            $content = $item['content'] ?? null;

            if (is_string($content) && trim($content) !== '') {
                $parts[] = trim($content);
            }
        }

        return trim(implode("\n\n", $parts));
    }
}
