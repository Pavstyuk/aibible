@extends('layouts.app')

@section('content')
    <section class="pb-2">
        @if (empty($email_key) || empty($email_address))
            <h1 class="text-center">{{ __('Создать аккаунт') }}</h1>
            <p class="text-center">
                На ваш почтовый адрес придет ссылка для установки пароля.
            </p>
            @include('partials.reg-form-email')
        @else
            <h1 class="text-center">{{ __('Подтвердить регистрацию') }}</h1>
            <p class="text-center">
                Для завершения регистрации введите пароль или воспользуйтесь автоматической генерацией сложного пароля.
            </p>
            @include('partials.reg-form-confirm')
        @endif

    </section>
@endsection
