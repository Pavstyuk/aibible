<h2 class="mt-0 mb-1 font-size-large">Книга {{ $abook_name }}</h2>
<ul id="track-list" class="track-list clear-list grid gap-05rem">
    @foreach ($track_list as $num => $li)
        <li>
            <button class="track-button" data-link="{{ $li }}" data-num="{{ $num }}" data-state="none"
                onclick="window.player.playThisTrack(this)">
                {{ $abook_name }}, глава {{ $num }}
            </button>
        </li>
    @endforeach
</ul>
