@extends('layouts.app')

@section('content')
    <article class="pb-2">
        @if (session('status'))
            <div class="message message-success text-center mb-2">
                {{ session('status') }}
            </div>
        @endif
        <h1 class="mt-2 mb-2">
            {{ __('Мой аккаунт') }}
        </h1>
        <div
            class="the-card-small  border-radius-small back-color-second flex flex-align-start flex-justify-space-between flex-wrap gap-1rem">
            <div>
                <p class="flex flex-align-center gap-05rem mb-05">
                    <span>Пользователь:</span>
                    <span>{{ $user_name }}</span>
                    <a class="text-color-second block flex-item-fix-auto" style="line-height: 0"
                        href="{{ route('edit-user', ['id' => $user_id]) }}">
                        <i class="bx bxs-edit font-size-bigger"></i>
                    </a>
                </p>
                <p class="flex flex-align-center flex-justify-space-between gap-1rem mb-0">
                    <span class="text-color-second"> Email: {{ Auth::user()->email }}</span>
                </p>
            </div>
            <a class="text-color-second flex flex-align-center gap-05rem flex-justify-end" href="/logout">
                Выход <i class="bx bxs-arrow-out-right-square-half"></i>
            </a>
        </div>

        <section class="mt-4">
            <h2 class="font-size-big">Мое меню</h2>
            <div class="grid grid-two-column gap-1rem">
                <div>
                    <a class="button button-gray htmx-button button-width-full"
                        href="{{ route('ai-comments-user', ['id' => $user_id]) }}">
                        {{ __('ИИ Комментарии') }}
                    </a>
                </div>
                <div>
                    <a class="button button-gray button-width-full"
                        href="{{ route('favorites-user', ['id' => $user_id]) }}">
                        {{ __('Избранные отрывки') }}
                    </a>
                </div>
            </div>
        </section>
    </article>
@endsection
