console.log("AI Bible Main Script");

if (
    window.matchMedia &&
    window.matchMedia("(prefers-color-scheme: dark)").matches
) {
    if (getCookie("apptheme") === "dark-mode") {
        document.body.classList.add("dark-mode");
        themeButton.querySelector(".bx").classList.add("bx-sun");
        themeButton.querySelector(".bx").classList.remove("bx-moon");
    }
}

window
    .matchMedia("(prefers-color-scheme: dark)")
    .addEventListener("change", (event) => {
        let newColorScheme = event.matches ? "dark" : "light";
        if (newColorScheme == "dark") {
            document.body.classList.add("dark-mode");
            themeButton.querySelector(".bx").classList.remove("bx-sun");
            themeButton.querySelector(".bx").classList.add("bx-moon");
        } else {
            document.body.classList.remove("dark-mode");
            themeButton.querySelector(".bx").classList.add("bx-sun");
            themeButton.querySelector(".bx").classList.remove("bx-moon");
        }
    });
function toggleTheme() {
    var themeButton = document.getElementById("toggle-theme-button");
    document.body.classList.toggle("dark-mode");
    if (document.body.classList.contains("dark-mode")) {
        // localStorage.setItem('theme', 'dark');
        setCookie("apptheme", "dark-mode", 365);
        themeButton.querySelector(".bx").classList.add("bx-sun");
        themeButton.querySelector(".bx").classList.remove("bx-moon");
    } else {
        setCookie("apptheme", "", 365);
        themeButton.querySelector(".bx").classList.remove("bx-sun");
        themeButton.querySelector(".bx").classList.add("bx-moon");
    }
}

function toggleBibleMenu() {
    var panel = document.getElementById("bible-menu-panel");
    var toggleButton = document.getElementById("toggle-menu-button");
    if (panel.dataset.status === "visible") {
        panel.dataset.status = "hidden";
        toggleButton.dataset.status = "close";
        document.documentElement.classList.remove("menu-visible");
        return false;
    }
    if (panel.dataset.status === "hidden") {
        panel.dataset.status = "visible";
        toggleButton.dataset.status = "open";
        document.documentElement.classList.add("menu-visible");
        return true;
    }
}

document.body.addEventListener("click", (e) => {
    // console.log(e.target);
    if (e.target.id === "bible-menu-panel") {
        toggleBibleMenu();
    }
});

function setCookie(cname, cvalue, exdays) {
    var date = new Date();
    date.setTime(date.getTime() + exdays * 24 * 60 * 60 * 1000);
    var expires = "expires=" + date.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(";");
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == " ") {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

document.addEventListener("DOMContentLoaded", () => {
    var chapterTexts = document.querySelectorAll(".chapter-text>p");
    if (chapterTexts) {
        chapterTexts.forEach((verse) => {
            verse.addEventListener("click", (e) => {
                if (e.target.nodeName !== "A" && e.target.nodeName !== "I") {
                    verse.classList.toggle("marked");
                }
            });
        });
    }
});

function checkIfPossible(button) {
    console.log(button);
    if (button.dataset.possible === "1") {
        return true;
    } else {
        button.parentElement.nextElementSibling.innerHTML = "";
        changePossible(button);
        return false;
    }
}

function changePossible(button) {
    if (button.dataset.possible === "1") {
        button.setAttribute("data-possible", "0");
        button.textContent = "Закрыть контекст";
    } else {
        button.setAttribute("data-possible", "1");
        button.textContent = "Контекст";
    }
}

function buildURL(a) {
    var url = a.href;
    console.log(url);

    var current = a.parentElement;
    var lastMarked = Number(current.querySelector("sub").textContent);

    var markedElements = [];

    while (current && current.classList.contains("marked")) {
        markedElements.push(current);
        current = current.previousElementSibling;
    }

    var firstMarked = Number(
        markedElements.pop().querySelector("sub").textContent,
    );

    if (lastMarked !== firstMarked) {
        var urlArray = url.split("/");
        urlArray[6] = `${firstMarked}-${urlArray[6]}`;
        url = urlArray.join("/");
    }
    window.location = url;
}

function setValues(token) {
    var commentElement = document.querySelector("#comment-content");

    var commentHTML = commentElement.innerHTML;
    var verse_id = commentElement.dataset.id;
    var translation = commentElement.dataset.translation;
    var model = commentElement.dataset.model;
    var user_id = commentElement.dataset.user;

    return {
        translation: translation,
        verse_id: verse_id,
        ai_comment: `${commentHTML}`,
        model: model,
        user_id: user_id,
        _token: token,
    };
}

function validateForm(form) {
    var result = false;
    var formKey = form.querySelector('[name="_key"]');
    var keyName = "aibible_form_key";
    var keyValue = "aibible_form_key_";
    keyValue += Date.now().toString().substring(0, 8);
    window.sessionStorage.setItem(keyName, keyValue);
    formKey.value = window.sessionStorage.getItem(keyName);
    if (formKey.value === keyValue) {
        result = true;
    }
    return result;
}

async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        console.log("Скопировано в буфер: ", text);
    } catch (err) {
        console.error("Ошибка копирования ", err);
    }
}
async function copyText(text) {
    try {
        await navigator.clipboard.writeText(text);
        console.log("Скопировано в буфер: ", text);
    } catch (err) {
        console.error("Ошибка копирования ", err);
    }
}

function buttonNotification(elem) {
    if (!elem.classList.contains("notification")) {
        elem.classList.add("notification");
        setTimeout(() => {
            elem.classList.remove("notification");
        }, 2000);
    } else {
        return false;
    }
}

function toggleNotification(elem) {
    if (!elem.classList.contains("notification")) {
        if (elem.querySelector(".bx").classList.contains("bxs-heart")) {
            elem.dataset.status = "Добавлено";
        }
        if (elem.querySelector(".bx").classList.contains("bx-heart")) {
            elem.dataset.status = "Удалено";
        }
    } else {
        return false;
    }
}

function togglePasswordInput(button, event) {
    event.preventDefault();
    var input = button.previousElementSibling;

    if (input.getAttribute("type") === "password") {
        input.setAttribute("type", "text");
        button.dataset.state = "visible";
    } else if (input.getAttribute("type") === "text") {
        input.setAttribute("type", "password");
        button.dataset.state = "hidden";
    }
}
