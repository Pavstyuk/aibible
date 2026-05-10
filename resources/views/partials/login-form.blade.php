<div class="the-card back-color-second border-radius-small mb-2">
    <form class="" action="{{ route('login.store') }}" method="post">
        <div class="mb-1">
            <label class="required" for="">{{ __('Email') }}</label>
            <input type="email" name="email" id="login-email" placeholder="{{ __('Email') }}" value=""
                autocomplete="" autofocus required>
        </div>
        <div class="mb-2">
            <label class="required" for="login-password">{{ __('Пароль') }}</label>
            <input class="password-input-toggle" type="password" name="password" id="login-password"
                placeholder="{{ __('Пароль') }}" value="" autocomplete="" required>
            <button class="password-toggle" data-state="hidden" role="button"
                onclick="togglePasswordInput(this, event)">
                <i class="bx bx-eye-alt"></i>
                <i class="bx bx-eye-closed"></i>
            </button>
        </div>
        <div class="mb-2">
            <label class="flex flex-align-center mb-0 ml-0 pl-0 pb-0" for="login-remember">
                <input type="checkbox" name="remember" id="login-remember" value="1">
                {{ __('Запомнить меня') }}</label>
            @csrf
        </div>
        <div class="mb-0">
            <button type="submit"
                class="button button-main button-width-full button-hover">{{ __('Войти') }}</button>
        </div>
    </form>
</div>
<p class="mb-0 text-center flex flex-justify-center gap-025rem font-size-small">
    <a class="text-color-second" href="{{ route('registration') }}">{{ __('Зарегистрироваться') }}</a>
    <span>|</span>
    <a class="text-color-second" href="{{ route('restore') }}">{{ __('Восстановить пароль') }}</a>
</p>
