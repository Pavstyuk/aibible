@php
    $siteName = config('app.fullname') ?: config('app.name', 'ИИ Библия');
    $siteUrl = rtrim((string) (config('app.url') ?: url('/')), '/');
    $fromAddress = config('mail.from.address') ?: 'hello@example.com';
@endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//RU" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">
    <style type="text/css">
        .body-wrap,
        body {
            width: 100% !important;
            height: 100%;
            background-color: #f5f5f5;
            line-height: 1.5;
            font-size: 18px;
            font-family: 'Inter', sans-serif;
            font-weight: 400
        }

        a {
            color: #087cbf;
            text-decoration: none
        }

        a:hover {
            text-decoration: underline
        }

        .text-center {
            text-align: center
        }

        .text-right {
            text-align: right
        }

        .text-left {
            text-align: left
        }

        .button {
            display: inline-block;
            color: #ffffff !important;
            background-color: #087cbf;
            font-weight: 400;
            border-radius: 4px;
            line-height: 2.5;
            padding: 0 18px
        }

        .button:hover {
            text-decoration: none;
            background-color: #259ade;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            color: #087cbf;
            margin-bottom: 20px;
            line-height: 1;
            font-weight: 700
        }

        h1 {
            font-size: 48px
        }

        h2 {
            font-size: 32px
        }

        h3 {
            font-size: 28px
        }

        h4 {
            font-size: 20px
        }

        h5 {
            font-size: 16px
        }

        ol,
        p,
        ul {
            font-size: 16px;
            margin-bottom: 20px;
            padding: 0
        }

        li {
            list-style: none;
            line-height: 1.5
        }

        b {
            font-weight: 800
        }

        .container {
            display: block !important;
            clear: both !important;
            margin: 0 auto !important;
            max-width: 580px !important
        }

        .container>table {
            width: 100% !important;
            border-collapse: collapse;
            border-radius: 18px;
            overflow: hidden
        }

        .container .masthead {
            padding: 0;
            background-color: #087cbf;
            color: #ffffff
        }

        .container .masthead img {
            display: block;
            width: 100%;
            height: auto;
        }

        .container .content {
            background: #fff;
            padding: 30px 35px
        }

        .container .content.footer {
            background: 0 0
        }

        .container .content.footer p {
            margin-bottom: 0;
            color: #545a68;
            text-align: center;
            font-size: 16px
        }

        .container .content.footer a {
            color: #545a68;
            text-decoration: none
        }

        .container .content.footer a:hover {
            text-decoration: underline
        }

        .container .content .verse-reference {
            color: #087cbf;
            font-weight: 700
        }

        .container .content .ai-comment {
            background: #f5f8fb;
            border-left: 4px solid #087cbf;
            border-radius: 8px;
            color: #1f2937;
            margin: 20px 0;
            padding: 18px 20px
        }

        .container .content .ai-comment> :first-child {
            margin-top: 0
        }

        .container .content .ai-comment> :last-child {
            margin-bottom: 0
        }

        .container .content .ai-comment h2,
        .container .content .ai-comment h3,
        .container .content .ai-comment h4 {
            color: #087cbf;
            font-size: 20px;
            line-height: 1.25;
            margin: 18px 0 10px
        }

        .container .content .ai-comment blockquote {
            border-left: 3px solid #cbd5e1;
            color: #475569;
            margin: 16px 0;
            padding-left: 15px
        }

        .container .content .ai-comment code,
        .container .content .ai-comment pre {
            background: #e8eef5;
            border-radius: 4px;
            font-family: monospace;
            white-space: pre-wrap
        }

        .container .content .ai-comment code {
            padding: 2px 4px
        }

        .container .content .ai-comment pre {
            padding: 10px
        }

        .container .content .ai-comment table {
            border-collapse: collapse;
            width: 100%
        }

        .container .content .ai-comment td,
        .container .content .ai-comment th {
            border: 1px solid #cbd5e1;
            padding: 6px;
            text-align: left
        }
    </style>
    <title>Комментарий ИИ к стиху {{ $verseReference }}</title>
</head>

<body>
    <table class="body-wrap">
        <tr>
            <td class="container">
                <table>
                    <tr>
                        <td align="center" class="masthead">
                            <img width="580" height="326" alt="{{ $siteName }}"
                                src="{{ $siteUrl }}/assets/img/mail-header.jpg">
                        </td>
                    </tr>
                    <tr>
                        <td class="content">
                            <h1>Ваш комментарий готов</h1>
                            @if ($userName !== '')
                                <p>Здравствуйте, {{ $userName }}!</p>
                            @else
                                <p>Здравствуйте!</p>
                            @endif
                            <p>Вы запросили комментарий к стиху
                                <span class="verse-reference">{{ $verseReference }}</span>
                                ({{ strtoupper($translation) }}).
                            </p>

                            <div class="ai-comment">
                                {!! $commentHtml !!}
                            </div>

                            @if ($model !== '')
                                <p style="color:#545a68;font-size:14px;">Модель: {{ $model }}</p>
                            @endif

                            <p style="text-align:center;">
                                <a class="button" href="{{ $verseUrl }}">Открыть стих на сайте</a>
                            </p>

                            <p>Или скопируйте ссылку в браузер:</p>
                            <p><a href="{{ $verseUrl }}">{{ $verseUrl }}</a></p>

                            <p>Вы получили это письмо, потому что запросили комментарий на сайте
                                {{ $siteName }}.</p>
                            <p>Комментарий не сохраняется автоматически. Если хотите вернуться к нему позже,
                                сохраните его на сайте.</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="content" style="background-color:#e7e7e7;" align="center">
                            <a href="mailto:{{ $fromAddress }}">{{ $fromAddress }}</a><br>
                        </td>
                    <tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="container">
                <table>
                    <tr>
                        <td class="content footer" align="center">
                            <p>{{ $siteName }}<br>
                                <a href="{{ $siteUrl }}">{{ config('app.name') ?: $siteName }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
