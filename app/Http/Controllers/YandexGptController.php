<?php

namespace App\Http\Controllers;

use App\Services\YandexGptClient;
use Illuminate\Http\Request;

class YandexGptController extends AIController
{
    public function ask(Request $request, YandexGptClient $yandexGpt)
    {
        return $this->respondWithProvider(
            $request,
            'yandexgpt',
            fn (string $question) => $yandexGpt->ask($question)
        );
    }
}
