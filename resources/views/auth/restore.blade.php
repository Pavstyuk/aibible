@extends('layouts.app')

@section('content')
    <section class="pb-2">
        <h1 class="text-center">{{ __('Восстановить пароль') }}</h1>
        <p class="text-center text-balance">
            Если ваша почта была зарегистрирована, не нее придет ссылка для восстановления пароля.
        </p>
        @include('partials.restore-form')

    </section>
@endsection
