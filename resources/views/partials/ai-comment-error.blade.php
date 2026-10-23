<article id="comment-content" contenteditable="true" data-id="{{ $verse_id }}" data-translation="{{ $translation }}"
    data-model="{{ $model ?? '' }}">
    {!! \App\Support\HtmlSanitizer::clean($comment) !!}
</article>
