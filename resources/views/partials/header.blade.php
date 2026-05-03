<header class="app-header">
    <div class="container-full flex flex-align-center flex-justify-space-between gap-1rem">
        <div class="app-logo">
            <a class="flex flex-align-center gap-05rem" href="{{ route('home') }}">
                <img class="flex-item-fix-auto image-block" width="40" height="40" decoding="async"
                    fetchpriority="high" src="/assets/svg/favicon.svg" alt="{{ config('app.name') }}">
                <span class="text-color-norm font-weight-600">{{ config('app.name') }}</span>
            </a>
        </div>
        <div class="flex gap-1rem flex-align-center flex-justify-end">
            @if (Route::has('login'))
                <nav class="flex gap-05rem font-size-small">
                    @auth
                        <a class="text-color-second" href="/user/{{ auth()->id() }}" class="">
                            {{ auth()->user()->name }}
                        </a>
                    @else
                        <a class="text-color-second" href="{{ route('login') }}" class="">
                            {{ __('Вход') }}
                        </a>
                    @endauth
                </nav>
            @endif
            <button id="toggle-theme-button" class="button-icon toggle-theme-button"
                aria-label="{{ __('Переключить тему светла/темная') }}"
                title="{{ __('Переключить тему светла/темная') }}" onclick="toggleTheme()" data-status="close">
                <i class='bx bx-moon'></i>
            </button>
            <button id="toggle-menu-button" class="button-icon toggle-menu-button"
                aria-label="{{ __('Открыть оглавление Библии') }}" title="{{ __('Оглавление Библии') }}"
                onclick="toggleBibleMenu()" data-status="close">
                <i class='bx bx-dots-horizontal-rounded'></i>
            </button>
        </div>
    </div>
</header>
