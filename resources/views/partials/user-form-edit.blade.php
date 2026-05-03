<div class="the-card back-color-second border-radius-small mb-2">
    @if ($errors->any())
        <div class="message message-error mb-2">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('update-user', ['id' => $user_id]) }}" method="post">
        <div class="mb-1">
            <label class="required" for="user-name">{{ __('Имя') }}</label>
            <input type="text" name="name" id="user-name" placeholder="{{ __('Имя') }}"
                value="{{ $user_name }}" required>
        </div>
        <div class="mb-1">
            <label class="required" for="reg-email">{{ __('Email') }}</label>
            <input type="email" name="email" id="reg-email" placeholder="{{ __('Email') }}"
                value="{{ $user_email }}" readonly required>
        </div>
        <div class="mb-2">
            @csrf
        </div>
        <div class="mb-0">
            <button type="submit"
                class="button button-main button-width-full button-hover">{{ __('Сохранить') }}</button>
        </div>
    </form>
</div>
