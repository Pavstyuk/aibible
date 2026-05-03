@extends('layouts.app')

@section('content')
    <article class="pb-2 pt-4">
        <nav class="width-full mb-2 flex flex-justify-space-between flex-align-center gap-1rem flex-align-center">
            <div>
                <a class="button-icon" href="{{ route('dashboard', ['id' => $user_id]) }}" aria-label="Назад" title="Назад">
                    <i class='bx bx-arrow-left-stroke'></i>
                </a>
            </div>
        </nav>
        @if (session('status'))
            <div class="message message-success text-center mb-2">
                {{ session('status') }}
            </div>
        @endif
        <h1 class="mt-2 mb-2">
            {{ __('Редактировать пользователя') }}
        </h1>

        @include('partials.user-form-edit')
    </article>
@endsection
