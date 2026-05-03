@extends('layouts.app')

@section('content')
    <div class="height-75vh pt-4">
        <div class="grid gap-2rem">
            <nav class="width-full flex flex-justify-space-between flex-align-center gap-1rem flex-align-center">
                <div>
                    <a class="button-icon" href="/{{ $translation }}/{{ $book_num }}/{{ $chapter_num }}"
                        aria-label="Вся Глава {{ $book }} {{ $chapter_num }}"
                        title="Вся Глава {{ $book }} {{ $chapter_num }}">
                        <i class='bx bx-arrow-left-stroke'></i>
                    </a>
                </div>
                @php
                    $verse_to_copy = '';
                    foreach ($text as $verse) {
                        $verse_to_copy .= "$verse->verse ";
                    }
                    $verse_to_copy .= "($book $chapter_num:$verses, $translation)";

                    if ($user_id > 0) {
                        $favorite_message = __('Добавлено');
                        $class_inactive = '';
                    } else {
                        $favorite_message = __('Необходимо войти');
                        $class_inactive = 'inactive';
                    }
                @endphp
                <div class="flex flex-justify-end flex-align-center flex-wrap gap-05rem">
                    @include('partials.button-favorite')
                    @include('partials.button-copy')
                    @include('partials.button-share')
                </div>
            </nav>
            <article class="width-full the-card border-radius-norm back-color-second">
                <h1 class="mb-0 mt-0 font-size-large">{{ $title }}</h1>

                <blockquote class="mb-0">
                    @foreach ($text as $verse)
                        <p class="text-balance mb-0"><sub>{{ $verse->verse_num }}</sub>{{ $verse->verse }}</p>
                    @endforeach
                    <p class="text-right mt-2 mb-0">
                        <cite><strong>{{ $book }} {{ $chapter_num }}:{{ $verses }}</strong></cite>
                    </p>
                </blockquote>
            </article>

            <nav class="width-full flex flex-justify-space-between gap-1rem flex-align-center">
                @if ($prev_verse_link)
                    <a class="button-icon" style="text-decoration: none" href="{{ $prev_verse_link }}"
                        aria-label="Предыдущий стих" title="Предыдущий стих">
                        <i class='bx bx-chevron-left'></i>
                    </a>
                @else
                    <span>&nbsp;</span>
                @endif
                @if ($next_verse_link)
                    <a class="button-icon" style="text-decoration: none" href="{{ $next_verse_link }}"
                        aria-label="Следующий стих" title="Следующий стих">
                        <i class='bx bx-chevron-right'></i>
                    </a>
                @endif
            </nav>
        </div>

        <div class="mt-6 width-full">
            @php
                $val = urlencode("$verse->verse ($book $chapter_num:$verses)");
            @endphp
            <h2 class="font-size-big text-center text-color-second">Функции искусственного интеллекта</h2>
            @if (!auth()->id())
                <p class="text-center font-size-small mb-2">Необходима <a href="{{ route('login') }}">войти в свой
                        аккаунт</a>,
                    чтобы использовать функционал ИИ</p>
            @endif

            <div class="grid grid-two-column gap-1rem mb-2 {{ $class_inactive }}">
                <button hx-get="{{ route('ask-ai', ['type' => 'comment']) }}" hx-target="#ai-comment" hx-trigger="click"
                    hx-vals='{"verse": "{{ $val }}", "verse_id": "{{ $verse_id }}", "translation": "{{ $translation }}", "_token": "{{ csrf_token() }}"}'
                    hx-swap="innerHTML" hx-on::after-request="this.remove()"
                    class="button button-gray htmx-button button-width-full">{{ __('Общий комментарий') }}</button>
                <button hx-get="{{ route('ask-ai', ['type' => 'lexical']) }}" hx-target="#ai-comment" hx-trigger="click"
                    hx-vals='{"verse": "{{ $val }}", "verse_id": "{{ $verse_id }}", "translation": "{{ $translation }}", "_token": "{{ csrf_token() }}"}'
                    hx-swap="innerHTML" hx-on::after-request="this.remove()"
                    class="button button-gray htmx-button button-width-full">{{ __('Лексический анализ') }}</button>

                <button hx-get="{{ route('ask-open-router', ['type' => 'questions']) }}" hx-target="#ai-comment"
                    hx-trigger="click"
                    hx-vals='{"verse": "{{ $val }}", "verse_id": "{{ $verse_id }}", "translation": "{{ $translation }}", "_token": "{{ csrf_token() }}"}'
                    hx-swap="innerHTML" hx-on::after-request="this.remove()"
                    class="button button-gray htmx-button button-width-full">{{ __('Вопросы для обсуждения') }}</button>
                <button hx-get="{{ route('ask-open-router', ['type' => 'sermon']) }}" hx-target="#ai-comment"
                    hx-trigger="click"
                    hx-vals='{"verse": "{{ $val }}", "verse_id": "{{ $verse_id }}", "translation": "{{ $translation }}", "_token": "{{ csrf_token() }}"}'
                    hx-swap="innerHTML" hx-on::after-request="this.remove()"
                    class="button button-gray htmx-button button-width-full">{{ __('Простая проповедь') }}</button>
            </div>
            <div id="ai-comment" class="ai-comment-wrapper mt-1"></div>

            @if (!empty($comments[0]))
                <details class="details width-full">
                    @php
                        $count = count($comments);
                    @endphp
                    <summary class="summary">Показать сохраненные комментарии ({{ $count }} шт.) к отрывку
                        {{ $book }}
                        {{ $chapter_num }}:{{ $verses }}</summary>
                    <section class="grid gap-2rem">
                        <header>
                            <h2 class="mb-1 font-size-large">{{ __('Комментарии к') }} {{ $book }}
                                {{ $chapter_num }}:{{ $verses }}
                            </h2>
                            <p class="mb-0">Перевод: {{ $translation }}</p>
                        </header>
                        @include('partials.ai-comments')

                    </section>
                </details>
            @endif
        </div>
    </div>
@endsection
