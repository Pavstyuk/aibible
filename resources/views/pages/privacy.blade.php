@extends('layouts.home')

@section('home')
    <article class="grid gap-2rem">
        <header
            class="grid-item column-span-2 the-card back-color-second border-radius-norm height-half flex flex-column flex-justify-end gap-1rem">
            <h1 class="mb-0 mt-0 width-read text-balance">
                <span class="text-color-second font-size-norm">{{ env('APP_NAME') }}</span><br />
                {{ __('Политика конфиденциальности') }}
            </h1>
        </header>
        <section class="pt-2 pb-4">

        </section>
    </article>
@endsection
