@extends('layouts.app')

@section('content')
    <article class="pb-2 chapter-text">

        @include('partials.menu-chapters')

        <h1 class="mb-2">{{ $book }} <br>
            <span class="font-size-bigger text-color-second">{{ __('Глава') }} {{ $chapter_num }}</span>
        </h1>

        @if ($audio_mp3 || $audio_ogg)
            <figure class="mb-2 width-fit">
                <figcaption class="font-size-smaller text-color-second mb-05"><b>Слушать книгу {{ $book }},
                        {{ __('Глава') }}
                        {{ $chapter_num }}</b>
                </figcaption>
                <div class="mb-05 border-radius-small">
                    <audio class="block" controls>
                        @if ($audio_mp3)
                            <source src="{{ config('app.url') . $audio_mp3 }}" type="audio/mpeg">
                        @endif
                        @if ($audio_ogg)
                            <source src="{{ config('app.url') . $audio_ogg }}" type="audio/ogg">
                        @endif
                    </audio>
                </div>
                <a class="block text-color-second font-size-tiny width-fit" href="{{ config('app.url') . $audio_mp3 }}"
                    download="">Скачать</a>
            </figure>
        @endif

        @foreach ($text as $verse)
            @if (in_array($verse->verse_num, $marked_arr))
                @php
                    $marked_class = 'class="marked"';
                @endphp
            @else
                @php
                    $marked_class = '';
                @endphp
            @endif
            <p {!! $marked_class !!}>
                <span>
                    <sub>{{ $verse->verse_num }}</sub>{{ $verse->verse }}
                </span>
                <a class="button-to-verse" onclick="event.preventDefault(); buildURL(this)"
                    href="/{{ $translation }}/{{ $book_num }}/{{ $chapter_num }}/{{ $verse->verse_num }}"
                    aria-label="Перейти к отрывку: {{ $book }} {{ $chapter_num }}:{{ $verse->verse_num }}"
                    title="Ссылка на страницу отрывка: {{ $book }} {{ $chapter_num }}:{{ $verse->verse_num }}">
                    <i class="bx bx-chevron-right"></i>
                </a>
            </p>
        @endforeach
    </article>

    <nav class=" flex flex-justify-space-between gap-1rem flex-align-center">
        @if ($prev_link)
            <a class="button button-gray" href="{{ $prev_link }}">{{ __('Назад') }}</a>
        @else
            <span>&nbsp;</span>
        @endif
        @if ($next_link)
            <a class="button button-gray" href="{{ $next_link }}">{{ __('Вперед') }}</a>
        @endif
    </nav>
@endsection
