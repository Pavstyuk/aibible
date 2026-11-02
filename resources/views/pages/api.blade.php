@extends('layouts.home')

@section('home')
    <article class="grid gap-2rem">
        <header
            class="grid-item column-span-2 the-card back-color-second border-radius-norm height-half flex flex-column flex-justify-end gap-1rem">
            <h1 class="mb-1 mt-0 width-read text-balance">
                Библия. Очень простой и быстрый API на Rust
            </h1>
            <p class="mb-0">Простой API-сервис для чтения Библии и поиска по тексту.
                Написан на Rust без веб-фреймворков: используется стандартный
                <code>TcpListener</code>, <a href="https://api.aibible.ru">api.aibible.ru</a>.
            </p>
        </header>
        <section class="pt-2 pb-4 width-read-wide">
            <h2>URL Адрес API</h2>
            <p>API готово для использования: <a href="https://api.aibible.ru">api.aibible.ru</a></p>


            <h2>Для самостоятельного развертывания</h2>
            <p>Код и документация на GitVerse:
                <a href="https://gitverse.ru/pavstyuk/bible_api_rust">https://gitverse.ru/pavstyuk/bible_api_rust</a>
            </p>

            <h2>Переводы</h2>
            Параметр get-запроса <code>translation</code>
            <table>
                <thead>
                    <tr>
                        <th>Код</th>
                        <th>Описание</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>rbo</code></td>
                        <td>Русский текст. Используется по умолчанию.</td>
                    </tr>
                    <tr>
                        <td><code>rst</code></td>
                        <td>Русский текст.</td>
                    </tr>
                    <tr>
                        <td><code>nasb</code></td>
                        <td>Английский текст (New American Standard Bible).</td>
                    </tr>
                </tbody>
            </table>

            <p>В каждом файле 66 книг. Названия книг в русских переводах русские, в <code>nasb</code> — английские.</p>

            <h2>Книги Библии</h2>
            Параметр get-запроса <code>book</code>
            <p>
                Названия книг для параметра <code>book</code>. Регистр не важен.
                Порядок строк — канонический; в JSON-файлах русских переводов порядок
                книг после Деяний отличается, но названия те же.
            </p>

            <table>
                <thead>
                    <tr>
                        <th>Русское название</th>
                        <th>Английское название (nasb)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Бытие</td>
                        <td>Genesis</td>
                    </tr>
                    <tr>
                        <td>Исход</td>
                        <td>Exodus</td>
                    </tr>
                    <tr>
                        <td>Левит</td>
                        <td>Leviticus</td>
                    </tr>
                    <tr>
                        <td>Числа</td>
                        <td>Numbers</td>
                    </tr>
                    <tr>
                        <td>Второзаконие</td>
                        <td>Deuteronomy</td>
                    </tr>
                    <tr>
                        <td>Иисус Навин</td>
                        <td>Joshua</td>
                    </tr>
                    <tr>
                        <td>Судьи</td>
                        <td>Judges</td>
                    </tr>
                    <tr>
                        <td>Руфь</td>
                        <td>Ruth</td>
                    </tr>
                    <tr>
                        <td>1 Царств</td>
                        <td>1 Samuel</td>
                    </tr>
                    <tr>
                        <td>2 Царств</td>
                        <td>2 Samuel</td>
                    </tr>
                    <tr>
                        <td>3 Царств</td>
                        <td>1 Kings</td>
                    </tr>
                    <tr>
                        <td>4 Царств</td>
                        <td>2 Kings</td>
                    </tr>
                    <tr>
                        <td>1 Паралипоменон</td>
                        <td>1 Chronicles</td>
                    </tr>
                    <tr>
                        <td>2 Паралипоменон</td>
                        <td>2 Chronicles</td>
                    </tr>
                    <tr>
                        <td>Ездра</td>
                        <td>Ezra</td>
                    </tr>
                    <tr>
                        <td>Неемия</td>
                        <td>Nehemiah</td>
                    </tr>
                    <tr>
                        <td>Есфирь</td>
                        <td>Esther</td>
                    </tr>
                    <tr>
                        <td>Иов</td>
                        <td>Job</td>
                    </tr>
                    <tr>
                        <td>Псалтирь</td>
                        <td>Psalms</td>
                    </tr>
                    <tr>
                        <td>Притчи</td>
                        <td>Proverbs</td>
                    </tr>
                    <tr>
                        <td>Екклесиаст</td>
                        <td>Ecclesiastes</td>
                    </tr>
                    <tr>
                        <td>Песня Песней</td>
                        <td>Song of Solomon</td>
                    </tr>
                    <tr>
                        <td>Исаия</td>
                        <td>Isaiah</td>
                    </tr>
                    <tr>
                        <td>Иеремия</td>
                        <td>Jeremiah</td>
                    </tr>
                    <tr>
                        <td>Плач Иеремии</td>
                        <td>Lamentations</td>
                    </tr>
                    <tr>
                        <td>Иезекииль</td>
                        <td>Ezekiel</td>
                    </tr>
                    <tr>
                        <td>Даниил</td>
                        <td>Daniel</td>
                    </tr>
                    <tr>
                        <td>Осия</td>
                        <td>Hosea</td>
                    </tr>
                    <tr>
                        <td>Иоиль</td>
                        <td>Joel</td>
                    </tr>
                    <tr>
                        <td>Амос</td>
                        <td>Amos</td>
                    </tr>
                    <tr>
                        <td>Авдий</td>
                        <td>Obadiah</td>
                    </tr>
                    <tr>
                        <td>Иона</td>
                        <td>Jonah</td>
                    </tr>
                    <tr>
                        <td>Михей</td>
                        <td>Micah</td>
                    </tr>
                    <tr>
                        <td>Наум</td>
                        <td>Nahum</td>
                    </tr>
                    <tr>
                        <td>Аввакум</td>
                        <td>Habakkuk</td>
                    </tr>
                    <tr>
                        <td>Софония</td>
                        <td>Zephaniah</td>
                    </tr>
                    <tr>
                        <td>Аггей</td>
                        <td>Haggai</td>
                    </tr>
                    <tr>
                        <td>Захария</td>
                        <td>Zechariah</td>
                    </tr>
                    <tr>
                        <td>Малахия</td>
                        <td>Malachi</td>
                    </tr>
                    <tr>
                        <td>Матфей</td>
                        <td>Matthew</td>
                    </tr>
                    <tr>
                        <td>Марк</td>
                        <td>Mark</td>
                    </tr>
                    <tr>
                        <td>Лука</td>
                        <td>Luke</td>
                    </tr>
                    <tr>
                        <td>Иоанн</td>
                        <td>John</td>
                    </tr>
                    <tr>
                        <td>Деяния</td>
                        <td>Acts</td>
                    </tr>
                    <tr>
                        <td>Римлянам</td>
                        <td>Romans</td>
                    </tr>
                    <tr>
                        <td>1 Коринфянам</td>
                        <td>1 Corinthians</td>
                    </tr>
                    <tr>
                        <td>2 Коринфянам</td>
                        <td>2 Corinthians</td>
                    </tr>
                    <tr>
                        <td>Галатам</td>
                        <td>Galatians</td>
                    </tr>
                    <tr>
                        <td>Ефесянам</td>
                        <td>Ephesians</td>
                    </tr>
                    <tr>
                        <td>Филиппийцам</td>
                        <td>Philippians</td>
                    </tr>
                    <tr>
                        <td>Колоссянам</td>
                        <td>Colossians</td>
                    </tr>
                    <tr>
                        <td>1 Фессалоникийцам</td>
                        <td>1 Thessalonians</td>
                    </tr>
                    <tr>
                        <td>2 Фессалоникийцам</td>
                        <td>2 Thessalonians</td>
                    </tr>
                    <tr>
                        <td>1 Тимофею</td>
                        <td>1 Timothy</td>
                    </tr>
                    <tr>
                        <td>2 Тимофею</td>
                        <td>2 Timothy</td>
                    </tr>
                    <tr>
                        <td>Титу</td>
                        <td>Titus</td>
                    </tr>
                    <tr>
                        <td>Филимону</td>
                        <td>Philemon</td>
                    </tr>
                    <tr>
                        <td>Евреям</td>
                        <td>Hebrews</td>
                    </tr>
                    <tr>
                        <td>Иаков</td>
                        <td>James</td>
                    </tr>
                    <tr>
                        <td>1 Петра</td>
                        <td>1 Peter</td>
                    </tr>
                    <tr>
                        <td>2 Петра</td>
                        <td>2 Peter</td>
                    </tr>
                    <tr>
                        <td>1 Иоанна</td>
                        <td>1 John</td>
                    </tr>
                    <tr>
                        <td>2 Иоанна</td>
                        <td>2 John</td>
                    </tr>
                    <tr>
                        <td>3 Иоанна</td>
                        <td>3 John</td>
                    </tr>
                    <tr>
                        <td>Иуда</td>
                        <td>Jude</td>
                    </tr>
                    <tr>
                        <td>Откровение</td>
                        <td>Revelation</td>
                    </tr>
                </tbody>
            </table>

            <p>
                В <code>rbo</code> книга называется «Софония», в <code>rst</code> —
                «Софоний». Остальные русские названия в этих файлах совпадают.
            </p>


            <h2>Эндпоинты</h2>

            <p>JSON в примерах ниже отформатирован с отступами для читаемости; сервер возвращает компактный JSON.</p>

            <h3>GET / — один стих</h3>

            <p>Возвращает текст одного стиха.</p>

            <table>
                <thead>
                    <tr>
                        <th>Параметр</th>
                        <th>Обязателен</th>
                        <th>По умолчанию</th>
                        <th>Описание</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>translation</code></td>
                        <td>нет</td>
                        <td><code>rbo</code></td>
                        <td>Код перевода: <code>rbo</code>, <code>rst</code> или <code>nasb</code>.</td>
                    </tr>
                    <tr>
                        <td><code>book</code></td>
                        <td>нет</td>
                        <td><code>Иоанн</code> (для <code>nasb</code> — <code>John</code>)</td>
                        <td>Название книги.</td>
                    </tr>
                    <tr>
                        <td><code>chapter</code></td>
                        <td>нет</td>
                        <td><code>1</code></td>
                        <td>Номер главы.</td>
                    </tr>
                    <tr>
                        <td><code>verse</code></td>
                        <td>нет</td>
                        <td><code>1</code></td>
                        <td>Номер стиха.</td>
                    </tr>
                </tbody>
            </table>

            <p>Пример:</p>

            <pre><code>curl 'https://api.aibible.ru/?translation=nasb&amp;book=John&amp;chapter=3&amp;verse=16'</code></pre>

            <p>Ответ:</p>

            <pre><code>{
  "info": "John 3:16",
  "text": "“For God so loved the world, that He gave His only Son, so that everyone who believes in Him will not perish, but have eternal life.",
  "translation": "nasb"
}</code></pre>

            <p>Поля ответа:</p>

            <ul>
                <li><code>text</code> — текст стиха; переводы строк заменены пробелами.</li>
                <li><code>info</code> — каноническое название книги, глава и стих.</li>
                <li><code>translation</code> — код перевода.</li>
            </ul>

            <p>
                Название книги сравнивается без учёта регистра. Первая буква может быть
                строчной, а цифру в начале названия можно писать слитно:
                <code>1коринфянам</code> будет распознано как <code>1 Коринфянам</code>.
            </p>

            <h3>GET /search — поиск по тексту</h3>

            <p>
                Ищет подстроку во всех стихах выбранного перевода. Поиск не учитывает
                регистр. Если книга не указана, поиск идёт по всей Библии.
            </p>

            <table>
                <thead>
                    <tr>
                        <th>Параметр</th>
                        <th>Обязателен</th>
                        <th>По умолчанию</th>
                        <th>Описание</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>query</code></td>
                        <td>да</td>
                        <td>—</td>
                        <td>Строка поиска длиной от 3 до 100 символов.</td>
                    </tr>
                    <tr>
                        <td><code>translation</code></td>
                        <td>нет</td>
                        <td><code>rbo</code></td>
                        <td>Код перевода.</td>
                    </tr>
                    <tr>
                        <td><code>book</code></td>
                        <td>нет</td>
                        <td>все книги</td>
                        <td>Ограничить поиск одной книгой.</td>
                    </tr>
                </tbody>
            </table>

            <p>Пример с русским текстом:</p>

            <pre><code>curl -G 'https://api.aibible.ru/search' \
  --data-urlencode 'query=начале' \
  --data-urlencode 'book=Бытие'</code></pre>

            <p>Ответ:</p>

            <pre><code>{
  "count": 1,
  "query": "начале",
  "results": [
    {
      "info": "Бытие 1:1",
      "text": "В начале сотворил Бог небо и землю.",
      "translation": "rbo"
    }
  ]
}</code></pre>

            <p>Поля ответа:</p>

            <ul>
                <li><code>results</code> — массив найденных стихов.</li>
                <li><code>count</code> — количество найденных стихов.</li>
                <li><code>query</code> — исходная строка поиска.</li>
            </ul>

            <p>
                Каждый элемент <code>results</code> содержит те же поля, что и ответ
                <code>GET /</code>: <code>text</code>, <code>info</code>, <code>translation</code>.
                Если ничего не найдено, возвращается <code>200 OK</code> с пустым массивом
                и <code>count: 0</code>. Ограничения на количество результатов и
                постраничной выдачи нет.
            </p>

            <h2>Ошибки</h2>

            <table>
                <thead>
                    <tr>
                        <th>Код</th>
                        <th>Тело ответа</th>
                        <th>Когда возникает</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>400</code></td>
                        <td><code>{"error": "Query is empty"}</code></td>
                        <td>Параметр <code>query</code> не задан или пуст.</td>
                    </tr>
                    <tr>
                        <td><code>400</code></td>
                        <td><code>{"error": "Query is too short"}</code></td>
                        <td>В <code>query</code> меньше 3 символов.</td>
                    </tr>
                    <tr>
                        <td><code>400</code></td>
                        <td><code>{"error": "Query is too long"}</code></td>
                        <td>В <code>query</code> больше 100 символов.</td>
                    </tr>
                    <tr>
                        <td><code>404</code></td>
                        <td><code>{"error": "Unknown translation"}</code></td>
                        <td>Неизвестный код перевода в <code>GET /</code>.</td>
                    </tr>
                    <tr>
                        <td><code>404</code></td>
                        <td><code>{"error": "Unknown book"}</code></td>
                        <td>Книга не найдена.</td>
                    </tr>
                    <tr>
                        <td><code>404</code></td>
                        <td><code>{"error": "Unknown chapter"}</code></td>
                        <td>Глава не найдена.</td>
                    </tr>
                    <tr>
                        <td><code>404</code></td>
                        <td><code>{"error": "Unknown verse"}</code></td>
                        <td>Стих не найден.</td>
                    </tr>
                    <tr>
                        <td><code>404</code></td>
                        <td><code>404 Not Found</code> (<code>text/plain</code>)</td>
                        <td>Неизвестный путь.</td>
                    </tr>
                </tbody>
            </table>

            <p>Пример ошибки:</p>

            <pre><code>curl 'https://api.aibible.ru/search?query=ab'</code></pre>

            <pre><code>{"error": "Query is too short"}</code></pre>
        </section>
    </article>
@endsection
