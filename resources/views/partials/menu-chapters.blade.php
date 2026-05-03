<nav class="pb-4">
    <p class="mb-1 text-color-second"><strong>Список глав книги {{ $book }} в перевод <span
                class="text-uppercase">{{ $translation }}<span></strong></p>
    <ul class="clear-list grid grid-chapters-column gap-05rem">
        @foreach ($chapters_nav as $c => $link)
        @if($c != $chapter_num)
        <li>
            <a class="button button-small button-width-full button-gray book-menu-item" href="{{ $link }}">{{ $c }}</a>
        </li>
        @else
        <li>
            <a class="button button-small button-width-full button-gray book-menu-item book-menu-current"
                href="{{ $link }}">{{ $c }}</a>
        </li>
        @endif
        @endforeach
    </ul>
</nav>