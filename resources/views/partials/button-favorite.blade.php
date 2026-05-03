<button class="button-icon button-favorite htmx-button {{ $class_inactive }}" data-status="{{ $favorite_message }}"
    @if ($user_id > 0) hx-post="{{ route('add-favorite') }}" hx-trigger="click" hx-target="this" hx-swap="innerHTML"
                        hx-vals='{"translation": "{{ $translation }}", "id_start": "{{ $verse_id }}", "id_end": "{{ $verse_id_end }}"}'
                        hx-on::after-request="toggleNotification(this); buttonNotification(this)"
                        @else
                            onclick="buttonNotification(this)" @endif
    aria-label="Добавить/удалить отрывок {{ $book }} {{ $chapter_num }}:{{ $verses }} в свое избранное"
    title="Добавить/удалить отрывок {{ $book }} {{ $chapter_num }}:{{ $verses }} в свое избранное">
    @if ($is_favorite)
        <i class='bx bxs-heart'></i>
    @else
        <i class='bx bx-heart'></i>
    @endif
</button>
