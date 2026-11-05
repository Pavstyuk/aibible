class Player {
    constructor() {
        this.trackNum = localStorage.getItem("currentTrack") ?? 1;
        this.volume = localStorage.getItem("volume") ?? 1;
        this.speed = localStorage.getItem("speed") ?? 1;
        this.audio = document.getElementById("audio");
        this.buttonPlay = document.getElementById("play-pause");
        this.volumeDisplay = document.getElementById("display-volume");
        this.volumeInput = document.getElementById("range-volume");
        this.speedDisplay = document.getElementById("display-speed");
        this.speedInput = document.getElementById("range-speed");
        this.positionDisplay = document.getElementById("display-position");
        this.positionInput = document.getElementById("range-position");
        this.durationDisplay = document.getElementById("display-duration");
        this.nameElement = document.getElementById("track-name");
        this.trackName = "";

        this.audio.addEventListener("ended", () => {
            if (!this.playNext()) {
                this.setPlayPauseButtonState("pause");
            }
        });
        this.audio.addEventListener("timeupdate", () => this.updatePosition());
        this.audio.addEventListener("loadedmetadata", () =>
            this.updateDuration(),
        );
        this.audio.addEventListener("play", () =>
            this.setMediaSessionPlaybackState("playing"),
        );
        this.audio.addEventListener("pause", () =>
            this.setMediaSessionPlaybackState("paused"),
        );

        this.setupMediaSession();
    }

    init() {
        if (document.querySelector(`[data-num="${this.trackNum}"]`)) {
            this.audio.src = document.querySelector(
                `[data-num="${this.trackNum}"]`,
            ).dataset.link;
        } else {
            this.audio.src =
                document.querySelector(`[data-num="1"]`).dataset.link;
            this.trackNum = 1;
        }
        this.setTrackActive(this.trackNum);

        this.setVolume(this.volume);
        this.volumeInput.value = this.volume;

        this.setSpeed(this.speed);
        this.speedInput.value = this.speed;
    }

    playPause() {
        if (this.audio.paused) {
            this.playAudio();
        } else {
            this.pauseAudio();
        }
    }

    playAudio() {
        const playPromise = this.audio.play();
        this.setPlayPauseButtonState("play");

        if (playPromise !== undefined) {
            playPromise.catch((error) => {
                if (error.name === "AbortError") return;
                console.error("Не удалось воспроизвести трек:", error);
                this.setPlayPauseButtonState("pause");
                this.setMediaSessionPlaybackState("paused");
            });
        }
    }

    pauseAudio() {
        this.audio.pause();
        this.setPlayPauseButtonState("pause");
    }

    playThisTrack(element) {
        this.audio.src = element.dataset.link;
        this.setSpeed(this.speed);
        this.setTrackActive(element.dataset.num);
        this.playAudio();
    }

    setPlayPauseButtonState(state) {
        if (state === "play") {
            this.buttonPlay.innerHTML = "<i class='bxf bx-pause'></i>";
            this.buttonPlay.setAttribute("data-state", "play");
        }
        if (state === "pause") {
            this.buttonPlay.innerHTML = "<i class='bxf bx-play'></i>";
            this.buttonPlay.setAttribute("data-state", "pause");
        }
    }

    setTrackActive(num) {
        var trackButtons = document.querySelectorAll(
            "#track-list .track-button",
        );
        trackButtons.forEach((button) => {
            if (button.dataset.num == num) {
                button.setAttribute("data-state", "active");
                this.updateName(button);
            } else {
                button.setAttribute("data-state", "none");
            }
        });
        this.updatePosition();
        this.trackNum = num;
        localStorage.setItem("currentTrack", num);
    }

    setVolume(level) {
        this.volume = level;
        this.audio.volume = this.volume;
        this.displayVolume();
        localStorage.setItem("volume", this.volume);
    }

    displayVolume() {
        this.volumeDisplay.value = Math.round(Number(this.volume) * 100);
    }

    setSpeed(val) {
        this.speed = val;
        this.audio.playbackRate = this.speed;
        this.displaySpeed();
        localStorage.setItem("speed", this.speed);
    }

    displaySpeed() {
        this.speedDisplay.value = this.speed;
    }

    playNext() {
        var nextTrack = Number(this.trackNum) + 1;
        var nextElement = document.querySelector(`[data-num="${nextTrack}"]`);
        if (!nextElement) return false;

        this.playThisTrack(nextElement);
        return true;
    }

    playPrev() {
        var prevTrack = Number(this.trackNum) - 1;
        var prevElement = document.querySelector(`[data-num="${prevTrack}"]`);
        if (!prevElement) return false;

        this.playThisTrack(prevElement);
        return true;
    }

    updateDuration() {
        var seconds = this.audio.duration;
        if (!Number.isFinite(seconds)) return;

        this.durationDisplay.value = this.formatTime(seconds);
        this.positionInput.max = seconds;
        this.updateMediaSessionPosition();
    }

    setPosition(val) {
        this.audio.currentTime = val;
        this.positionDisplay.value = this.formatTime(this.audio.currentTime);
        this.updateMediaSessionPosition();
    }

    updatePosition() {
        this.positionDisplay.value = this.formatTime(this.audio.currentTime);
        this.positionInput.value = this.audio.currentTime;
        this.updateMediaSessionPosition();
    }

    updateName(track) {
        this.trackName = track.textContent.trim();
        this.updateMediaSession(track);

        if (this.nameElement) {
            this.nameElement.textContent = `${this.trackName}:`;
            return true;
        } else {
            return false;
        }
    }

    setupMediaSession() {
        if (!("mediaSession" in navigator)) return;

        var setHandler = (action, handler) => {
            try {
                navigator.mediaSession.setActionHandler(action, handler);
            } catch (error) {
                // Браузер не поддерживает это действие — это нормально.
            }
        };

        setHandler("play", () => {
            if (this.audio.paused) this.playAudio();
        });
        setHandler("pause", () => {
            if (!this.audio.paused) this.pauseAudio();
        });
        setHandler("previoustrack", () => this.playPrev());
        setHandler("nexttrack", () => this.playNext());

        setHandler("seekbackward", (details) => {
            var offset = details.seekOffset || 10;
            this.setPosition(Math.max(0, this.audio.currentTime - offset));
        });
        setHandler("seekforward", (details) => {
            var offset = details.seekOffset || 10;
            var target = this.audio.currentTime + offset;
            this.setPosition(
                Number.isFinite(this.audio.duration)
                    ? Math.min(target, this.audio.duration)
                    : target,
            );
        });
        setHandler("seekto", (details) => {
            if (details.seekTime == null) return;
            this.setPosition(details.seekTime);
        });
    }

    setMediaSessionPlaybackState(state) {
        if ("mediaSession" in navigator) {
            navigator.mediaSession.playbackState = state;
        }
    }

    updateMediaSession(track) {
        if (!("mediaSession" in navigator) || !("MediaMetadata" in window)) {
            return;
        }

        navigator.mediaSession.metadata = new MediaMetadata({
            title: track.textContent.trim(),
            artist: "Онлайн Библия",
            album:
                document
                    .querySelector("#list-wrapper h2")
                    ?.textContent.trim() ?? "",
            artwork: [
                {
                    src: new URL(
                        "/assets/favicon/web-app-manifest-512x512.png",
                        location.origin,
                    ).href,
                    sizes: "512x512",
                    type: "image/png",
                },
            ],
        });
    }

    updateMediaSessionPosition() {
        if (
            !("mediaSession" in navigator) ||
            !navigator.mediaSession.setPositionState
        ) {
            return;
        }

        var duration = this.audio.duration;
        if (!Number.isFinite(duration) || duration <= 0) return;

        try {
            navigator.mediaSession.setPositionState({
                duration: duration,
                playbackRate: this.audio.playbackRate || 1,
                position: Math.min(Math.max(0, this.audio.currentTime), duration),
            });
        } catch (error) {
            // Некоторые браузеры могут бросить исключение — игнорируем.
        }
    }

    formatTime(seconds) {
        if (isNaN(seconds)) return "0:00";
        var mins = Math.floor(seconds / 60);
        var secs = Math.floor(seconds % 60)
            .toString()
            .padStart(2, "0");
        return `${mins}:${secs}`;
    }
}

var player = new Player();
player.init();
