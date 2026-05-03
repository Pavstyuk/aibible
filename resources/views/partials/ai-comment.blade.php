<article id="comment-content" contenteditable="true" data-id="{{ $verse_id }}" data-translation="{{ $translation }}"
    data-model="{{ $model ?? '' }}" data-user="{{ Auth::id() }}">
    {!! $comment !!}
</article>
<button class="button button-gray button-width-full" hx-post="{{ route('save-ai') }}" hx-swap="outerHTML" hx-trigger="click"
    hx-target="this" hx-vals='js:{...setValues("{{ csrf_token() }}")}'>
    {{ __('Сохранить комментарий') }}
</button>
