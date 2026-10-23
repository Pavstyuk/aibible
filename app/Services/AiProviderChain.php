<?php

namespace App\Services;

use App\Exceptions\AiProvidersFailedException;
use Throwable;

class AiProviderChain
{
    public function __construct(
        private readonly GigaChatClient $gigaChat,
        private readonly YandexGptClient $yandexGpt,
        private readonly LmStudioClient $lmStudio
    ) {
    }

    /**
     * Try providers in order and return the first successful answer.
     *
     * @return array{answer: string, model: string, usage: array<string, mixed>|null, http_code: int, provider: string, fallback_errors: array<string, string>}
     */
    public function ask(string $prompt): array
    {
        $providers = [
            'gigachat' => $this->gigaChat,
            'yandexgpt' => $this->yandexGpt,
            'lmstudio' => $this->lmStudio,
        ];

        $errors = [];

        foreach ($providers as $name => $provider) {
            try {
                $result = $provider->ask($prompt);
                $result['provider'] = $name;
                $result['fallback_errors'] = $errors;

                return $result;
            } catch (Throwable $exception) {
                $errors[$name] = $exception->getMessage();
            }
        }

        throw new AiProvidersFailedException($errors);
    }
}
