@extends('layouts.app')

@section('content')
    <article class="pb-2">

        @if ($search_query)
            <h1 class="mb-2 font-size-large">
                {{ __('Результаты поиска по запросу: ') }} <i>{{ $search_query }}</i>
            </h1>
        @else
            <h1 class="mb-2 font-size-large">{{ __('Поиск по Библии') }}</h1>
        @endif
        <div class="the-card back-color-second border-radius-norm mb-4">
            <form class="grid grid-two-column gap-1rem" action="{{ route('search') }}" method="get">
                <div class="grid-item column-span-2">
                    <label class="required" for="searching">{{ __('Поиск') }}</label>
                    <input type="search" name="searching" id="searching" placeholder="{{ __('Поиск') }}"
                        value="{{ $search_query }}" minlength="3" required>
                </div>
                <div class="grid-item">
                    <select class="chevron" name="search-translation" id="search-translation" value="">
                        @foreach ($bibles as $t_slug => $t_name)
                            <option value="{{ $t_slug }}" {{ $t_slug == $search_translation ? 'selected' : '' }}>
                                {{ $t_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid-item">
                    <select class="chevron" name="search-book" id="search-book" value="">
                        <option value="" {{ $search_book == '' ? 'selected' : '' }}>{{ __('Все книги') }}</option>
                        @foreach ($books_list as $b_int => $b_name)
                            <option value="{{ $b_int }}" {{ $b_int == $search_book ? 'selected' : '' }}>
                                {{ $b_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid-item column-span-2 mt-1">
                    <button type="submit"
                        class="button button-main button-width-full button-hover">{{ __('Искать') }}</button>
                </div>
            </form>
        </div>

        @if ($search_query && count($search_results) > 0)
            @foreach ($search_results as $verse)
                <div class="mb-4">
                    <p class="mb-05">
                        <em>
                            @php
                                echo preg_replace("/$search_query/iu", "<b>$search_query</b>", $verse->verse);
                            @endphp
                        </em>
                        <br><cite>({{ $verse->book_name }} {{ $verse->chapter_num }}:{{ $verse->verse_num }})</cite>
                    </p>
                    <p class="mb-0 text-right flex flex-justify-end gap-05rem flex-align-center">
                        <button class="button button-small button-gray htmx-button"
                            hx-get="{{ route('textChapter', [
                                'translation' => $search_translation,
                                'book_num' => $verse->book_num,
                                'chapter_num' => $verse->chapter_num,
                                'marked_verse' => $verse->verse_num,
                            ]) }}"
                            hx-swap="innerHTML" hx-trigger="click[checkIfPossible(this)]" hx-target="next .chapter-wrapper"
                            hx-on::after-request="changePossible(this)" data-possible="1"
                            title="{{ __('Отрывок в контексте главы') }}">
                            {{ __('Контекст') }}
                        </button>
                        <a class="button button-small button-gray"
                            href="/{{ $search_translation }}/{{ $verse->book_num }}/{{ $verse->chapter_num }}?marked={{ $verse->verse_num }}"
                            target="_blank" title="{{ __('Перейти в главе') }}">
                            {{ $verse->book_name }} {{ $verse->chapter_num }}:{{ $verse->verse_num }}
                        </a>
                        <a class="button button-small button-icon"
                            href="/{{ $search_translation }}/{{ $verse->book_num }}/{{ $verse->chapter_num }}/{{ $verse->verse_num }}"
                            target="_blank" title="{{ __('Перейти к отрывку') }}">
                            <i class="bx bx-chevron-right"></i>
                        </a>
                    </p>
                    <div class="chapter-wrapper"></div>
                </div>
            @endforeach
        @endif

        @if ($search_query && count($search_results) === 0)
            <div class="mb-3">
                <p class="mb-1">
                    По вашему запросу <i>{{ $search_query }}</i> ничего не найдено.<br>
                    Попробуйте другие слова.
                </p>
            </div>
        @endif
    </article>
@endsection
