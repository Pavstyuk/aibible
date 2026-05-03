<aside id="bible-menu-panel" class="bible-menu-wrapper" data-status="hidden">
    <div class="bible-menu-content">
        <p class="mb-1 text-color-second"><b>Меню</b></p>
        <nav class="mb-2 flex flex-justify-start gap-1rem flex-wrap font-size-norm font-weight-500">
            <a class="text-color-norm flex flex-align-center gap-05rem" href="{{ Route('search') }}">
                <i class="bx bx-scan-search font-size-big block text-color-second"></i> <span>Поиск</span>
            </a>
            <a class="text-color-norm flex flex-align-center gap-05rem" href="{{ Route('random') }}">
                <i class="bx bx-shuffle font-size-big block text-color-second"></i> <span>Случайный отрывок</span>
            </a>
            @if (auth()->id() > 0)
                <a class="text-color-norm flex flex-align-center gap-05rem"
                    href="{{ Route('favorites-user', ['id' => auth()->id()]) }}">
                    <i class="bx bx-heart font-size-big block text-color-second"></i> <span>Избранное</span>
                </a>
            @endif
        </nav>
        <div hx-get="{{ route('menu-translations', ['book_num' => $book_num, 'chapter_num' => $chapter_num]) }}"
            hx-swap="innerHTML" hx-trigger="load" hx-target="this"></div>
        <div hx-get="{{ route('menu-books', ['book_num' => $book_num, 'chapter_num' => $chapter_num]) }}"
            hx-swap="innerHTML" hx-trigger="load" hx-target="this"></div>
    </div>
</aside>
