<button class="button-icon button-share" data-status="Ссылка в буфере"
    onclick="copyToClipboard('{{ url('') }}/{{ $translation }}/{{ $book_num }}/{{ $chapter_num }}/{{ $verses }}');
    buttonNotification(this)"
    aria-label="Копировать адрес и поделить стихом из Библии {{ $book }} {{ $chapter_num }}:{{ $verses }}"
    title="Копировать адрес чтобы поделить стихом из Библии {{ $book }} {{ $chapter_num }}:{{ $verses }}">
    <i class='bx bx-share'></i>
    <i class='bx bx-clipboard-check'></i>
</button>
