@include('partials.bible-menu')

<link rel="stylesheet" href="/assets/fonts/inter-tight/inter-tight.min.css">
<link rel="stylesheet" href="/assets/fonts/boxicons/boxicons.min.css">

<a id="to-top" class="to-top" href="#top">
    <i class="bx bx-chevron-up"></i>
</a>

<footer
    class="app-footer back-color-second the-card flex flex-column gap-1rem flex-justify-center flex-align-center font-weight-norm">
    <nav class="flex flex-wrap gap-1rem justify-content-center align-items-center">
        <a class="font-size-tiny text-color-second" href="{{ route('policy') }}">Политика конфиденциальности</a>
        <a class="font-size-tiny text-color-second" href="{{ route('privacy') }}">Согласие на обработку ПД</a>
    </nav>
    <p class="mb-0 font-size-tiny text-color-second">
        <span>{{ env('APP_NAME', 'AI Bible') }}, <?= '2025-' . date('Y') ?></span>
    </p>
    <p class="mb-0 font-size-tiny text-color-second">
        Версия {{ env('APP_VER', '1.0') }}
    </p>
</footer>
<script defer id="htmx-library" src="/assets/libs/htmx.min.js"></script>
<script defer id="main-scripts" src="/assets/js/main.min.js?ver={{ env('APP_VER', time()) }}"></script>
<script async id="ya-metrika" src="/assets/js/ya.min.js"></script>

@stack('js')
