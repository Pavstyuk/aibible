<header id="app-header" class="app-header">
    <div class="container-full flex flex-align-center flex-justify-space-between gap-1rem">
        <div class="app-logo">
            <div class="flex flex-align-center gap-05rem">
                <a href="{{ route('home') }}">
                    <img class="flex-item-fix-auto image-block" width="40" height="40" decoding="async"
                        fetchpriority="high" src="/assets/svg/favicon.svg"
                        alt="{{ __('Логотип сайта: Библия и звездочки искры') }}">
                </a>
                @if (Route::is('chapter'))
                    <span class="font-size-small text-color-norm font-weight-600">
                        {{ $book }} {{ $chapter_num }}
                    </span>
                @elseif(Route::is('single'))
                    <span class="font-size-small text-color-norm font-weight-600">
                        {{ $book }} {{ $chapter_num }}:{{ $verses }}
                    </span>
                @else
                    <a href="{{ route('home') }}">
                        <span class="font-size-small text-color-norm font-weight-600">{{ config('app.name') }}</span>
                    </a>
                @endif
            </div>
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
            <button id="toggle-menu-button" class="button-icon toggle-menu-button"
                aria-label="{{ __('Открыть оглавление Библии') }}" title="{{ __('Оглавление Библии') }}"
                onclick="toggleBibleMenu()" data-status="close">
                <i class='bx bx-dots-horizontal-rounded'></i>
            </button>
        </div>
    </div>
</header>
