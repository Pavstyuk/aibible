<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class GigaChatClient
{
    private const TOKEN_CACHE_KEY = 'gigachat_access_token';

    /**
     * Send a prompt to GigaChat and return the generated text.
     *
     * @return array{answer: string, model: string, usage: array<string, mixed>|null, http_code: int}
     */
    public function ask(string $prompt): array
    {
        $token = $this->accessToken();
        $response = $this->sendChatRequest($token, $prompt);

        // A cached token can be revoked or expired on the GigaChat side.
        // Refresh it once and retry the same chat request.
        if ($response->status() === 401) {
            Cache::forget(self::TOKEN_CACHE_KEY);
            $token = $this->accessToken(true);
            $response = $this->sendChatRequest($token, $prompt);
        }

        if (! $response->successful()) {
            throw new RuntimeException(
                'GigaChat chat request failed: HTTP ' . $response->status()
                . ' ' . Str::limit($response->body(), 1000, '')
            );
        }

        $body = $response->json();

        if (! is_array($body)) {
            throw new RuntimeException('GigaChat returned an invalid JSON response.');
        }

        $answer = data_get($body, 'choices.0.message.content');

        if (! is_string($answer) || trim($answer) === '') {
            throw new RuntimeException('GigaChat returned an empty response.');
        }

        return [
            'answer' => trim($answer),
            'model' => (string) ($body['model'] ?? config('ai.gigachat.model', 'GigaChat-3-Ultra')),
            'usage' => is_array($body['usage'] ?? null) ? $body['usage'] : null,
            'http_code' => $response->status(),
        ];
    }

    /**
     * Return a cached access token or request a new one from the OAuth endpoint.
     */
    public function accessToken(bool $forceRefresh = false): string
    {
        if (! $forceRefresh) {
            $cached = Cache::get(self::TOKEN_CACHE_KEY);

            if (is_string($cached) && $cached !== '') {
                return $cached;
            }
        }

        $response = Http::asForm()
            ->withHeaders([
                'Accept' => 'application/json',
                'RqUID' => (string) Str::uuid(),
                'Authorization' => 'Basic ' . $this->authorizationKey(),
            ])
            ->withOptions($this->httpOptions())
            ->connectTimeout((int) config('ai.gigachat.connect_timeout', 10))
            ->timeout((int) config('ai.gigachat.timeout', 90))
            ->post((string) config('ai.gigachat.oauth_url'), [
                'scope' => (string) config('ai.gigachat.scope', 'GIGACHAT_API_PERS'),
            ]);

        if (! $response->successful()) {
            throw new RuntimeException(
                'GigaChat OAuth request failed: HTTP ' . $response->status()
                . ' ' . Str::limit($response->body(), 1000, '')
            );
        }

        $body = $response->json();

        // Current responses use access_token/expires_at. Older Sber endpoints
        // and some SDK versions use tok/exp, so accept both formats.
        $token = data_get($body, 'access_token') ?? data_get($body, 'tok');

        if (! is_string($token) || trim($token) === '') {
            throw new RuntimeException('GigaChat OAuth response does not contain an access token.');
        }

        $expiresAt = (int) (data_get($body, 'expires_at') ?? data_get($body, 'exp') ?? 0);

        // Different API versions returned milliseconds or seconds.
        if ($expiresAt > 100_000_000_000) {
            $expiresAt = intdiv($expiresAt, 1000);
        }

        $ttl = $expiresAt > time()
            ? max(60, $expiresAt - time() - 60)
            : 1500;

        Cache::put(self::TOKEN_CACHE_KEY, $token, now()->addSeconds($ttl));

        return $token;
    }

    private function sendChatRequest(string $token, string $prompt): Response
    {
        $payload = [
            'model' => (string) config('ai.gigachat.model', 'GigaChat-3-Ultra'),
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => (float) config('ai.gigachat.temperature', 0.7),
            'stream' => false,
            'repetition_penalty' => 1.0,
        ];

        $maxTokens = (int) config('ai.gigachat.max_tokens', 2000);

        if ($maxTokens > 0) {
            $payload['max_tokens'] = $maxTokens;
        }

        return Http::withToken($token)
            ->acceptJson()
            ->withOptions($this->httpOptions())
            ->connectTimeout((int) config('ai.gigachat.connect_timeout', 10))
            ->timeout((int) config('ai.gigachat.timeout', 90))
            ->post((string) config('ai.gigachat.chat_url'), $payload);
    }

    private function authorizationKey(): string
    {
        $authKey = trim((string) config('ai.gigachat.auth_key', ''));

        if (str_starts_with(strtolower($authKey), 'basic ')) {
            $authKey = trim(substr($authKey, 6));
        }

        if ($authKey !== '') {
            return $authKey;
        }

        $clientId = trim((string) config('ai.gigachat.client_id', ''));
        $clientSecret = trim((string) config('ai.gigachat.client_secret', ''));

        if ($clientId !== '' && $clientSecret !== '') {
            return base64_encode($clientId . ':' . $clientSecret);
        }

        throw new RuntimeException(
            'GigaChat credentials are not configured: set GIGACHAT_AUTH_KEY or GIGACHAT_CLIENT_ID/GIGACHAT_CLIENT_SECRET.'
        );
    }

    /**
     * @return array{verify: bool|string}
     */
    private function httpOptions(): array
    {
        $caBundle = trim((string) config('ai.gigachat.ca_bundle', ''));

        return [
            'verify' => $caBundle !== '' ? $caBundle : true,
        ];
    }
}
