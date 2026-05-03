@extends('layouts.app')

@section('content')

<section class="pb-2 text-balance">
    <h1 class="mb-2">{{ __('Написать заметку') }}</h1>

    <form action="{{ route('note.create', ['user'=>$user]) }}" method="post" >
        <div class="mb-2">
            <label class="required mb-05" for="reg-name">{{ __('Название') }}</label>
            <input type="text" name="title" id="note-title" value="" autofocus required>
        </div>
        <div class="mb-2">
            <label class="required mb-05" for="reg-name">{{ __('Ссылка') }}</label>
            <input type="text" name="slug" id="note-slug" readonly value="" required>
        </div>
        <div class="mb-2">
            <label class="required mb-05" for="note-content">{{ __('Заметка') }}</label>
            <input type="hidden" name="content" id="note-content" required>
            <trix-editor input="note-content"></trix-editor>
        </div>
        <div class="mb-0">
            <button type="submit" class="button button-main button-width-full button-hover">{{ __('Сохранить') }}</button>
        </div>
    </form>
</section>
@endsection


@once
    
@push('css')

<link rel="stylesheet" href="/assets/libs/trix/trix.min.css" />
    
@endpush

@push('js')

<script defer src="/assets/libs/trix/trix.min.js"></script>
    
@endpush

@endonce