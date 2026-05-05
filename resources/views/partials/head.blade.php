<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $seo_title }}</title>
<meta name="description" content="{{ $seo_description }}">

<link rel="preload" href="/assets/fonts/inter-tight/inter-tight.min.css" as="style">
<link rel="preload" href="/assets/fonts/boxicons/boxicons.min.css" as="style">
<link rel="preload" href="/assets/css/main.min.css?ver={{ env('APP_VER', time()) }}" as="style">
<link rel="preload" href="/assets/css/custom.min.css?ver={{ env('APP_VER', time()) }}" as="style">

<link rel="stylesheet" href="/assets/css/main.min.css?ver={{ env('APP_VER', time()) }}">
<link rel="stylesheet" href="/assets/css/custom.min.css?ver={{ env('APP_VER', time()) }}">

<link rel="apple-touch-icon" href="/assets/favicon/apple-touch-icon.png">
<link rel="icon" type="image/x-icon" href="/assets/favicon/favicon.ico">
<link rel="icon" type="image/png" sizes="96x96" href="/assets/favicon/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="192x192" href="/assets/favicon/web-app-manifest-192x192.png">
<link rel="icon" type="image/png" sizes="512x512" href="/assets/favicon/web-app-manifest-512x512.png">
<link rel="alternate icon" type="image/png" files="apple-touch-icon.png, favicon.png" />
<meta name="msapplication-TileColor" content="#087cbf">
<link rel="manifest" href="/manifest.json">

@stack('css')
