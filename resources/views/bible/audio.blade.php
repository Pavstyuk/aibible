@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="/assets/css/audio.min.css?ver={{ config('app.ver') }}">
    <article class="pb-2 pt-2">
        <h1 class="mb-1 mt-0 text-center font-size-large">
            Слушать Библию Онлайн
        </h1>
        <section class="">
            <h2 class="mt-0 mb-1 font-size-big text-center text-color-second">Выбор перевода и книги</h2>
            <form id="player-form" method="get" action=""
                class="back-color-main border-thin border-color-gray border-radius-small the-card-small mb-2 grid gap-1rem">
                @if ($audio_translations ?? null)
                    <select name="atrans" id="atrans" class="chevron border-radius-tiny" hx-get="/htmx/load"
                        hx-target="#list-wrapper" hx-swap="innerHTML" hx-trigger="change"
                        hx-include='[name="abooknum"], [name="atrans"]'
                        hx-on::after-request="window.player.init(); window.player.pauseAudio();">
                        @foreach ($audio_translations as $translation)
                            <option value="{{ $translation }}" @if ($translation == $audio_translation) selected @endif>
                                {{ strtoupper($translation) }}
                            </option>
                        @endforeach
                    </select>
                @endif
                @if ($books_list ?? null)
                    <select name="abooknum" id="abooknum" class="chevron border-radius-tiny" hx-get="/htmx/load"
                        hx-target="#list-wrapper" hx-swap="innerHTML" hx-trigger="change"
                        hx-include='[name="atrans"], [name="abooknum"]'
                        hx-on::after-request="window.player.init(); window.player.pauseAudio();">
                        @foreach ($books_list as $i => $book)
                            <option value="{{ $i }}" @if ($i == $audio_book_num) selected @endif>
                                {{ $book }}
                            </option>
                        @endforeach
                    </select>
                @endif
                @csrf
            </form>

            <div class="">
                <div class="flex flex-align-center flex-justify-center gap-1rem mb-1">
                    <button id="switch-prev" class="button-audio button-prev" onclick="window.player.playPrev()"
                        aria-label="Назад">
                        <i class='bxf bx-skip-previous'></i>
                    </button>
                    <button id="play-pause" class="button-audio button-play" onclick="window.player.playPause(this)"
                        data-state="pause" aria-label="Включить / пауза">
                        <i class='bxf bx-play'></i>
                    </button>
                    <button id="switch-next" class="button-audio button-next" onclick="window.player.playNext()"
                        aria-label="Вперед">
                        <i class="bxf bx-skip-next"></i>
                    </button>
                </div>
                <div class="mb-05">
                    <label for="range-position" class="flex flex-align-center gap-05rem">
                        <i class="bx bx-rotate-cw"></i> <span id="track-name" aria-live="polite"></span> <span><output
                                id="display-position" for="range-position" class="display-position"
                                value="--:--">--:--</output> / <output id="display-duration" for="range-position"
                                class="display-duration" value="--:--">--:--</output></span>
                    </label>
                    <input type="range" name="range-position" id="range-position" class="input-range range-position"
                        min="0" max="1" step="1" value="0"
                        oninput="window.player.setPosition(this.value)">
                </div>
                <div class="mb-05">
                    <label for="range-volume" class="flex flex-align-center gap-025rem">
                        <i class="bxf bx-volume-full"></i> <span>Громкость: </span>
                        <span><output id="display-volume" for="range-volume" value="1"></output>%</span>
                    </label>
                    <input type="range" name="range-volume" id="range-volume" class="input-range range-volume"
                        min="0" max="1" step="0.05" value="1"
                        oninput="window.player.setVolume(this.value)">
                </div>
                <div>
                    <label for="range-speed" class="flex flex-align-center gap-025rem">
                        <i class='bxf bx-timer'></i> <span>Скорость: </span>
                        <span>x<output id="display-speed" for="range-speed" value="1"></output></span>
                    </label>
                    <input type="range" name="range-speed" id="range-speed" class="input-range range-speed" min="0.5"
                        max="2" step="0.1" value="1" oninput="window.player.setSpeed(this.value)">

                </div>
            </div>
            <div class="hidden">
                <audio id="audio" class="block" src="" controls></audio>
            </div>
        </section>

        @if ($track_list ?? null)
            <section id="list-wrapper"
                class="list-wrapper back-color-main border-thin border-color-gray border-radius-small the-card-small mb-2 mt-2">
                @include('partials.track-list')
            </section>
        @endif
    </article>
    <script defer src="/assets/js/audio.min.js?ver={{ config('app.ver') }}"></script>
@endsection
