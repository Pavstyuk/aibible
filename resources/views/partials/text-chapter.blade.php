<article>
    <h2 class="font-size-big text-color-second mt-0">{{ $book }} {{ $chapter_num }}</h2>
    @foreach ($text as $verse)
        @if ((int) $verse->verse_num === (int) $marked_verse)
            <p><sub>{{ $verse->verse_num }}</sub><b>{{ $verse->verse }}</b></p>
        @else
            <p><sub>{{ $verse->verse_num }}</sub>{{ $verse->verse }}</p>
        @endif
    @endforeach
</article>
