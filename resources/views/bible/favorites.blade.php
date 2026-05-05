@extends('layouts.app')

@section('content')
    <div class="height-75vh pt-2">
        <nav class="width-full mb-2 flex flex-justify-space-between flex-align-center gap-1rem flex-align-center">
            <div>
                <a class="button-icon" href="{{ route('dashboard', ['id' => $user_id]) }}" aria-label="Назад" title="Назад">
                    <i class='bx bx-arrow-left-stroke'></i>
                </a>
            </div>
        </nav>
        <header>
            <h1>Мои избранные отрывки</h1>
        </header>
        <div class="grid gap-2rem">
            @if ($favorites)
                @php
                    $favorite_message = __('Удалено');
                    $class_inactive = '';
                    $is_favorite = true;
                @endphp
                @foreach ($favorites as $trans => $verses_arr)
                    <section class="grid gap-1rem width-full">
                        <h2>Стихи из перевода <span class="text-uppercase">{{ $trans }}</span></h2>
                        @foreach ($verses_arr as $verse)
                            @if (is_array($verse))
                                @php
                                    $grouped_verse = '';
                                    foreach ($verse as $v) {
                                        $grouped_verse .= $v->verse . ' ';
                                    }
                                    $translation = $trans;
                                    $verse_id = $verse[0]->id;
                                    $verse_id_end = end($verse)->id;
                                    $book = $verse[0]->book_name;
                                    $chapter_name = $verse[0]->chapter_num;
                                    $verses = $verse[0]->verse_num . '-' . end($verse)->verse_num;
                                    $verse_to_copy = $grouped_verse . ' ' . "($book $chapter_num:$verses)";
                                @endphp
                                <article class="the-card-small back-color-second border-radius-small line-height-norm">
                                    <h3 class="mt-0 mb-05 font-size-bigger">
                                        {{ $book }}
                                        {{ $chapter_name }}:{{ $verse[0]->verse_num }}-{{ end($verse)->verse_num }}
                                    </h3>
                                    <p id="{{ $translation }}-verse-{{ $verse_id }}" class="mb-05">
                                        {{ $grouped_verse }}
                                    </p>
                                    <div class="flex flex-justify-end flex-align-center gap-05rem">
                                        @include('partials.button-favorite')
                                        @include('partials.button-copy')
                                        @include('partials.button-share')
                                    </div>
                                </article>
                                @php
                                    continue;
                                @endphp
                            @endif
                            @php
                                $translation = $trans;
                                $verse_id = $verse->id;
                                $verse_id_end = $verse->id;
                                $book = $verse->book_name;
                                $chapter_name = $verse->chapter_num;
                                $verses = $verse->verse_num;
                                $verse_to_copy = $verse->verse . ' ' . "($book $chapter_num:$verses)";
                            @endphp
                            <article class="the-card-small back-color-second border-radius-small">
                                <h3 class="mt-0 mb-05 font-size-bigger">
                                    {{ $book }} {{ $chapter_name }}:{{ $verses }}
                                </h3>
                                <p id="{{ $translation }}-verse-{{ $verse_id }}" class="mb-05">
                                    {{ $verse->verse }}

                                </p>
                                <div class="flex flex-justify-end flex-align-center gap-05rem">
                                    @include('partials.button-favorite')
                                    @include('partials.button-copy')
                                    @include('partials.button-share')
                                </div>
                            </article>
                        @endforeach

                    </section>
                @endforeach
            @endif
        </div>
    </div>
@endsection
