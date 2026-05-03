@extends('layouts.app')

@section('content')
    <div class="flex flex-column flex-justify-center flex-align-center gap-2rem height-75vh">
        <h1 class="mt-0 mb-2 text-balance">Случайный отрывок из Библии</h1>

        <form class="grid grid-two-column gap-1rem width-full mb-2" action="" method="get">
            <div class="grid-item  column-span-2">
                <select class="chevron" name="translation" id="search-translation" value="">
                    @foreach ($bibles as $t_slug => $t_name)
                        <option value="{{ $t_slug }}" {{ $t_slug == $translation ? 'selected' : '' }}>
                            {{ $t_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid-item column-span-2">
                <button type="submit"
                    class="button button-main button-width-full button-hover">{{ __('Получить') }}</button>
            </div>
        </form>

        <article class="width-full the-card border-radius-norm back-color-second">
            <h2 class="mb-0 mt-0 font-size-big">{{ $title }}</h2>
            <blockquote class="mb-0">
                @foreach ($text as $verse)
                    <p class="text-balance mb-0"><sub>{{ $verse->verse_num }}</sub>{{ $verse->verse }}</p>
                @endforeach
                <p class="text-right mt-2 mb-0">
                    <cite><strong>{{ $book }} {{ $chapter_num }}:{{ $verses }}</strong></cite>
                </p>
            </blockquote>
        </article>
        <nav class="width-full flex flex-justify-end gap-1rem flex-align-center">
            <a class="button button-gray" style="text-decoration: none"
                href="/{{ $translation }}/{{ $book_num }}/{{ $chapter_num }}?marked={{ $text[0]->verse_num }}"
                aria-label="Отрывок в контексте главы {{ $book }} {{ $chapter_num }}"
                title="Отрывок в контексте главы {{ $book }} {{ $chapter_num }}">
                {{ $book }} {{ $chapter_num }}:{{ $verses }}
            </a>
        </nav>
    </div>
@endsection
