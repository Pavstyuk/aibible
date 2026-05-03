<blockquote class="mb-0 mt-0 line-height-norm">
    <p class="font-size-bigger">
        {{ $verse_display }}
    </p>
    <cite class="block font-size-norm text-right font-weight-600">
        {{ $book_title }} {{ $chapter_num }}:{{ $verse_num }} <span>{{ $translation }}<span>
    </cite>
</blockquote>
<a class="button button-gray button-width-full"
    href="/{{ $translation }}/{{ $book_num }}/{{ $chapter_num }}?marked={{ $verse_num }}">{{ __('Открыть главу') }}</a>
