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
    <form action="" hx-post="{{ route('send-reg-link') }}" hx-trigger="submit[validateForm(this)]" hx-target="this"
        hx-swap="outerHTML" method="post">
        <div class="mb-1">
            <label class="required" for="reg-email">{{ __('Email') }}</label>
            <input type="email" name="email" id="reg-email" placeholder="{{ __('Email') }}"
                value="{{ old('email') }}" autocomplete="email" required>
        </div>
        <div class="mb-2">
            @csrf
            <input type="hidden" name="_key" value="{{ csrf_token() }}">
            <label class="flex flex-align-center mb-0 ml-0 pl-0 pb-0" for="reg-privacy">
                <input type="checkbox" name="privacy" id="reg-privacy" value="1" required>
                <span>Я принимаю <a href="/privacy">условия обработки</a> персональных данных
                    и <a href="/policy">политику конфиденциальности</a></span>
            </label>
        </div>
        <div class="mb-0">
            <button type="submit"
                class="button button-main button-width-full button-hover">{{ __('Получить ссылку') }}</button>
        </div>
    </form>
</div>
<p class="mb-0 text-center flex flex-justify-center gap-025rem font-size-small">
    <a class="text-color-second font-size-small" href="{{ route('login') }}">{{ __('Войти') }}</a>
    <span>|</span>
    <a class="text-color-second" href="{{ route('restore') }}">{{ __('Восстановить пароль') }}</a>
</p>
