<?php

namespace App\Exceptions;

use RuntimeException;

class AiProvidersFailedException extends RuntimeException
{
    /**
     * @param array<string, string> $errors
     */
    public function __construct(public readonly array $errors)
    {
        parent::__construct(
            'All AI providers failed: ' . json_encode(
                $errors,
                JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
            )
        );
    }
}
