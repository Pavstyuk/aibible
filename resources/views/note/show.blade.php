@extends('layouts.app')

@section('content')

<section class="pb-2 text-balance">
    <h1>{{ __('Заметкa') }} {{ $note_slug }} {{ __('от') }} {{ $user_name }}</h1>

    {{-- @auth --}}
    <footer class="font-size-tiny mt-4 border-top-thin border-color-gray pt-1">
        <a href="{{ route('note.edit', [ 'id' => $note_id, 'user' => $user ]) }}">{{ __('Редактировать') }}</a>
    </footer>
    {{-- @endauth --}}
</section>

@endsection