<nav class="pb-2">
    <p class="mb-1 text-color-second"><strong>Переводы</strong></p>
    <ul class="clear-list grid grid-three-column gap-05rem">
        @foreach ($list as $li)
        @if($li['slug'] != $translation)
        <li>
            <a class="button button-small button-gray button-width-full book-menu-item"
                href="/{{ $li['slug'] }}/{{ $book_num }}/{{ $chapter_num }}">
                {{ $li['name'] }}
            </a>
        </li>
        @else
        <li>
            <a class="button button-small button-gray button-width-full book-menu-item book-menu-current"
                href="/{{ $li['slug'] }}/{{ $book_num }}/{{ $chapter_num }}">
                {{ $li['name'] }}
            </a>
        </li>
        @endif
        @endforeach
    </ul>
</nav>