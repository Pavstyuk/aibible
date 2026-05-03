<div class="the-card back-color-second border-radius-small mb-2">
    <form hx-post="{{ route('send-reg-link') }}" hx-trigger="submit[validateForm(this)]" hx-target="this"
        hx-swap="outerHTML" method="post">
        <div class="mb-2">
            <label class="required" for="restore-name">{{ __('Email') }}</label>
            <input type="email" name="email" id="restore-name" placeholder="{{ __('Email') }}" value=""
                autocomplete="{{ old('email') }}" required>
            <input type="hidden" name="_restore" value="1">
            <input type="hidden" name="_key" value="{{ csrf_token() }}">
            @csrf
        </div>
        <div class="mb-0">
            <button type="submit"
                class="button button-main button-width-full button-hover htmx-button">{{ __('Получить ссылку') }}</button>
        </div>
    </form>
</div>
<p class="mb-0 text-center flex flex-justify-center gap-025rem font-size-small">
    <a class="text-color-second font-size-small" href="{{ route('login') }}">{{ __('Войти') }}</a>
    <span>|</span>
    <a class="text-color-second" href="{{ route('registration') }}">{{ __('Зарегистрироваться') }}</a>
</p>
