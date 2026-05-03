<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $seo_title }}</title>
<meta name="description" content="{{ $seo_description }}">

<link rel="stylesheet" href="/assets/fonts/inter-tight/inter-tight.min.css">
<link rel="stylesheet" href="/assets/css/main.min.css?ver={{ env('APP_VER', time()) }}">
<link rel="stylesheet" href="/assets/css/custom.css?ver={{ env('APP_VER', time()) }}">

<link rel="preconnect" href="/assets/fonts/boxicons/boxicons.min.css">

@stack('css')