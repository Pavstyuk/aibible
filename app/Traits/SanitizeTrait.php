<?php

/**
 * Trait Clear Parameters 
 * 
 */

namespace App\Traits;

trait SanitizeTrait
{
    public function clearIntParameter(string $param): int
    {
        return (int) preg_replace("/\D/", '', $param);
    }

    public function clearTranslationParameter(string $param): string
    {
        return preg_replace(
            "/[^a-z]/",
            '',
            strtolower($param)
        );
    }

    public function isValidTranslation(string $translation): bool
    {
        return in_array($translation, config('bible.translations', []), true);
    }
}
