<button class="button-icon button-copy" data-text="{{ $verse_to_copy }}" data-status="Отрывок буфере"
    onclick="copyText(this.dataset.text); buttonNotification(this)"
    aria-label="Копировать отрывок {{ $book }} {{ $chapter_num }}:{{ $verses }} в буфер обмена"
    title="Копировать отрывок {{ $book }} {{ $chapter_num }}:{{ $verses }} в буфер обмена">
    <i class='bx bx-copy-list'></i>
    <i class='bx bx-copy-check'></i>
</button>
