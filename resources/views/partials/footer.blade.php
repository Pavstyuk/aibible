@include('partials.bible-menu')

<link rel="stylesheet" href="/assets/fonts/boxicons/boxicons.min.css">

<footer class="app-footer back-color-second the-card flex flex-column gap-1rem flex-justify-center flex-align-center">
    <nav class="flex flex-wrap gap-1rem justify-content-center align-items-center">
        <a class="font-size-small text-color-second" href="{{ route('privacy') }}">Политика конфиденциальности</a>
    </nav>
    <p class="mb-0 font-size-tiny text-color-second">
        <span>{{ env('APP_NAME', 'AI Bible') }}, <?= '2025-' . date('Y') ?></span>
    </p>
    <p class="mb-0 font-size-tiny text-color-second">
        Версия {{ env('APP_VER', '1.0') }}
    </p>
</footer>
<script defer id="htmx-library" src="/assets/libs/htmx.min.js"></script>
<script defer id="main-scripts" src="/assets/js/main.js"></script>

@stack('js')
