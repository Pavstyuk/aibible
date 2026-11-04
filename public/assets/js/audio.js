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
        this.audio.play();
        this.setPlayPauseButtonState("play");
        this.audio.onended = () => {
            this.playNext();
        };
        this.audio.ontimeupdate = () => {
            this.updatePosition();
        };
    }

    pauseAudio() {
        this.audio.pause();
        this.setPlayPauseButtonState("pause");
        this.audio.addEventListener("ended", () => {
            this.playNext();
        });
    }

    playThisTrack(element) {
        var link = element.dataset.link;
        var num = element.dataset.num;
        this.audio.src = link;
        this.audio.currentTime = 0;
        this.setSpeed(this.speed);
        this.setTrackActive(num);
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
        trackButtons.forEach(function (button) {
            if (button.dataset.num == num) {
                button.setAttribute("data-state", "active");
            } else {
                button.setAttribute("data-state", "none");
            }
        });
        this.setDuration();
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
        if (document.querySelector(`[data-num="${nextTrack}"]`)) {
            this.trackNum = nextTrack;
            this.playThisTrack(
                document.querySelector(`[data-num="${this.trackNum}"]`),
            );
        } else {
            return false;
        }
    }

    playPrev() {
        var prevTrack = Number(this.trackNum) - 1;
        if (document.querySelector(`[data-num="${prevTrack}"]`)) {
            this.trackNum = prevTrack;
            this.playThisTrack(
                document.querySelector(`[data-num="${this.trackNum}"]`),
            );
        } else {
            return false;
        }
    }

    setDuration() {
        this.audio.onloadedmetadata = () => {
            var second = this.audio.duration;
            console.log("Duration: " + second);
            this.durationDisplay.value = this.formatTime(second);
            this.positionInput.max = this.audio.duration;
        };
    }

    setPosition(val) {
        this.audio.currentTime = val;
        this.positionDisplay.value = this.formatTime(this.audio.currentTime);
    }

    updatePosition() {
        this.positionDisplay.value = this.formatTime(this.audio.currentTime);
        this.positionInput.value = this.audio.currentTime;
    }

    formatTime(seconds) {
        if (isNaN(seconds)) return "0:00";
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60)
            .toString()
            .padStart(2, "0");
        return `${mins}:${secs}`;
    }
}

var player = new Player();
player.init();
