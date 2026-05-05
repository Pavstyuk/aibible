<div class="the-card back-color-second border-radius-small mb-2">
    @if ($errors->any())
        <div class="message message-error mb-2">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('registration.store') }}" method="post">
        <div class="mb-1">
            <label class="required" for="reg-email">{{ __('Email') }}</label>
            <input type="email" name="email" id="reg-email" placeholder="{{ __('Email') }}"
                value="{{ $email_address }}" readonly required>
        </div>
        <div class="mb-1">
            <label class="required" for="reg-password">{{ __('Пароль') }}</label>
            <input class="password-input-toggle" type="password" name="password" id="reg-password"
                placeholder="{{ __('Пароль') }}" value="" autocomplete="new-password" required>
            <button class="password-toggle" data-state="hidden" role="button"
                onclick="togglePasswordInput(this, event)">
                <i class="bx bx-eye-alt"></i>
                <i class="bx bx-eye-closed"></i>
            </button>
        </div>
        <div class="mb-2">
            <label class="required" for="reg-password-confirm">{{ __('Пароль подтверждение') }}</label>
            <input class="password-input-toggle-conf" type="password" name="password-confirm" id="reg-password-confirm"
                placeholder="{{ __('Пароль подтверждение') }}" value="" autocomplete="new-password" required>
            <button class="password-toggle" data-state="hidden" role="button"
                onclick="togglePasswordInput(this, event)">
                <i class="bx bx-eye-alt"></i>
                <i class="bx bx-eye-closed"></i>
            </button>
        </div>
        <div class="mb-2">
            <label class="flex flex-align-center mb-0 ml-0 pl-0 pb-0" for="reg-privacy">
                <input type="checkbox" name="privacy" id="reg-privacy" value="1" required>
                <span>Я принимаю <a href="/privacy">условия обработки</a> персональных данных
                    и <a href="/policy">политику конфиденциальности</a></span>
            </label>
            @csrf
        </div>
        <div class="mb-0">
            <button type="submit"
                class="button button-main button-width-full button-hover">{{ __('Зарегистрироваться') }}</button>
        </div>
    </form>
</div>
<p class="mb-0 text-center flex flex-justify-center gap-025rem font-size-small">
    <a class="text-color-second font-size-small" href="{{ route('login') }}">{{ __('Войти') }}</a>
    <span>|</span>
    <a class="text-color-second" href="{{ route('restore') }}">{{ __('Восстановить пароль') }}</a>
</p>
