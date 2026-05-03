@extends('layouts.app')
@section('content')
    <section class="pb-2">

        @if (!empty($user_id))
            <p class="the-card back-color-success text-color-success border-radius-small border-thin">
                Успешный вход. Добро пожаловать {{ $user_name }}
            </p>
        @endif

        @if (!auth()->id())
            <h1 class="text-center">{{ __('Войти в аккаунт') }}</h1>
            @include('partials.login-form')
        @else
            <div class="the-card text-center ">
                <div class="mb-2">
                    <a class="button button-main button-width-full" href="{{ route('home') }}">На главную</a>
                </div>
                <div class="mb-2">
                    <a class="button button-gray button-width-full" href="{{ route('dashboard', ['id', auth()->id()]) }}">Мой
                        аккаунт</a>
                </div>
                <div>
                    <a class="button button-transparent" href="{{ route('logout') }}">Выход</a>
                </div>
            </div>
        @endif

    </section>
@endsection
