@extends('layouts.app')

@section('content')
    <article class="pb-2 chapter-text">

        @include('partials.menu-chapters')

        <h1 class="mb-2">{{ $book }} <br>
            <span class="font-size-bigger text-color-second">{{ __('Глава') }} {{ $chapter_num }}</span>
        </h1>

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
                    href="/{{ $translation }}/{{ $book_num }}/{{ $chapter_num }}/{{ $verse->verse_num }}">
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
