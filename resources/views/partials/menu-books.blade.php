<nav class="pb-2">
    <p class="mb-1 text-color-second"><strong>Книги Ветхого Завета</strong></p>
    <ul class="clear-list flex flex-wrap gap-05rem flex-justify-stretch">
        @foreach ($books_old as $name => $link)
            @if ($name != $book)
                <li class="flex-item-auto">
                    <a class="button button-small button-gray button-width-full book-menu-item"
                        href="{{ $link }}">{{ $name }}</a>
                </li>
            @else
                <li class="flex-item-auto">
                    <a class="button button-small button-gray button-width-full book-menu-item book-menu-current"
                        href="{{ $link }}">{{ $name }}</a>
                </li>
            @endif
        @endforeach
    </ul>
    <p class="mb-1 mt-2 text-color-second"><strong>Книги Нового Завета</strong></p>
    <ul class="clear-list flex flex-wrap gap-05rem flex-justify-stretch">
        @foreach ($books_new as $name => $link)
            @if ($name != $book)
                <li class="flex-item-auto">
                    <a class="button button-small button-gray button-width-full book-menu-item"
                        href="{{ $link }}">{{ $name }}</a>
                </li>
            @else
                <li class="flex-item-auto">
                    <a class="button button-small button-gray button-width-full book-menu-item book-menu-current"
                        href="{{ $link }}">{{ $name }}</a>
                </li>
            @endif
        @endforeach
    </ul>
</nav>
