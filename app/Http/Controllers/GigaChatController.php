<?php

namespace App\Http\Controllers;

use App\Services\GigaChatClient;
use Illuminate\Http\Request;

class GigaChatController extends AIController
{
    public function ask(Request $request, GigaChatClient $gigaChat)
    {
        return $this->respondWithProvider(
            $request,
            'gigachat',
            fn (string $question) => $gigaChat->ask($question)
        );
    }
}
