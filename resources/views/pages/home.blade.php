@extends('layouts.home')

@section('home')
    <article class="grid gap-2rem">
        <div class="grid grid-three-column gap-1rem">
            <header
                class="grid-item column-span-2 the-card back-color-second border-radius-norm height-half flex flex-column flex-justify-end gap-1rem">
                <h1 class="mb-0 mt-0 width-read text-balance">
                    <span class="text-color-second font-size-norm">{{ env('APP_NAME') }}</span><br />
                    {!! env('APP_FULLNAME') !!}
                </h1>
                <p class="mb-2 width-read text-balance">
                    {{ env('APP_DESC') }}
                </p>
                <nav class="flex flex-wrap gap-1rem">
                    @php
                        if (Cookie::get('book_num') && Cookie::get('chapter_num')) {
                            $button_text = __('Продолжить чтение');
                        } else {
                            $button_text = __('Читать с начала');
                        }
                    @endphp
                    <a class="button button-main button-hover button-mobile-width-full"
                        href="/{{ Cookie::get('translation', 'rbo') }}/{{ Cookie::get('book_num', '1') }}/{{ Cookie::get('chapter_num', '1') }}">{{ $button_text }}</a>
                </nav>
            </header>
            <div class="grid-item home-random-verse the-card-small border-thin border-color-gray border-radius-norm flex flex-column gap-2rem flex-justify-space-between"
                hx-get="{{ route('random-verse') }}" hx-swap="innerHTML" hx-trigger="load, every 7s" hx-target="this">
            </div>
        </div>
        <section class="pt-2 pb-4">
            <h2 class="mt-0">{{ __('Новые ИИ комментарии') }}</h2>
            @if ($comments)
                <div class="two-column-masonry">
                    @foreach ($comments as $comment)
                        @include('partials.ai-item')
                    @endforeach
                </div>
            @endif
        </section>
    </article>
@endsection
