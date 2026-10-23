<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI providers
    |--------------------------------------------------------------------------
    |
    | Only use proxy URLs that you control. A proxy receives your API key.
    | Direct provider APIs can be configured here without changing controllers.
    |
    */

    'timeout' => (int) env('AI_TIMEOUT', 90),

    // Send the generated comment to the authenticated user by email.
    'email_notifications' => (bool) env('AI_EMAIL_NOTIFICATIONS', true),

    'yandexgpt' => [
        'url' => env('YANDEX_GPT_URL', 'https://ai.api.cloud.yandex.net/v1/responses'),
        'api_key' => env('YANDEX_API_KEY'),
        'folder_id' => env('YANDEX_FOLDER_ID'),
        'model' => env('YANDEX_GPT_MODEL', 'gpt-oss-120b/latest'),
        'temperature' => (float) env('YANDEX_GPT_TEMPERATURE', 0.7),
        'max_output_tokens' => (int) env('YANDEX_GPT_MAX_OUTPUT_TOKENS', 5000),
        'timeout' => (int) env('YANDEX_GPT_TIMEOUT', 90),
        'connect_timeout' => (int) env('YANDEX_GPT_CONNECT_TIMEOUT', 10),
    ],

    'gigachat' => [
        'oauth_url' => env('GIGACHAT_OAUTH_URL', 'https://ngw.devices.sberbank.ru:9443/api/v2/oauth'),
        'chat_url' => env('GIGACHAT_CHAT_URL', 'https://api.giga.chat/v1/chat/completions'),
        'auth_key' => env('GIGACHAT_AUTH_KEY'),
        'client_id' => env('GIGACHAT_CLIENT_ID'),
        'client_secret' => env('GIGACHAT_CLIENT_SECRET'),
        'scope' => env('GIGACHAT_SCOPE', 'GIGACHAT_API_PERS'),
        'model' => env('GIGACHAT_MODEL', 'GigaChat-3-Ultra'),
        'temperature' => (float) env('GIGACHAT_TEMPERATURE', 0.7),
        'max_tokens' => (int) env('GIGACHAT_MAX_TOKENS', 2000),
        'timeout' => (int) env('GIGACHAT_TIMEOUT', 90),
        'connect_timeout' => (int) env('GIGACHAT_CONNECT_TIMEOUT', 10),
        'ca_bundle' => env('GIGACHAT_CA_BUNDLE'),
    ],

    'lmstudio' => [
        'url' => env('LM_STUDIO_URL', 'http://host.docker.internal:1234/api/v1/chat'),
        'model' => env('LM_STUDIO_MODEL', 'google/gemma-4-12b-qat'),
        'api_key' => env('LM_STUDIO_API_KEY'),
        'temperature' => (float) env('LM_STUDIO_TEMPERATURE', 0.5),
        'max_output_tokens' => (int) env('LM_STUDIO_MAX_OUTPUT_TOKENS', 3000),
        'reasoning' => env('LM_STUDIO_REASONING', 'off'),
        'timeout' => (int) env('LM_STUDIO_TIMEOUT', 180),
        'connect_timeout' => (int) env('LM_STUDIO_CONNECT_TIMEOUT', 10),
    ],

];
