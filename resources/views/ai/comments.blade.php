@extends('layouts.app')

@section('content')
    <div class="height-75vh pt-4">
        <nav class="width-full mb-2 flex flex-justify-space-between flex-align-center gap-1rem flex-align-center">
            <div>
                <a class="button-icon" href="{{ route('dashboard', ['id' => $user_id]) }}" aria-label="Назад" title="Назад">
                    <i class='bx bx-arrow-left-stroke'></i>
                </a>
            </div>
        </nav>
        <header class="mb-2">
            <h1>Мои сгенерированные комментарии</h1>
        </header>
        <div class="grid gap-2rem">
            @if ($comments && count($comments) > 0)
                @include('partials.ai-comments')
            @else
                <p class="">
                    {{ __('Комментарии появятся здесь после того, как вы их добавите. Это можно сделать на странице библейского стиха.') }}
                </p>
            @endif
        </div>
    </div>
@endsection
