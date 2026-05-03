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

        <title>Ссылка на восстановление пароля</title>
        <style type="text/css">
            /* Basic resets for email clients */
            body {
                margin: 0;
                padding: 0;
                background-color: #f6f6f6;
                font-family: 'Inter', sans-serif;
            }

            table {
                border-spacing: 0;
                width: 100%;
            }

            img {
                border: 0;
            }

            .container {
                width: 100%;
                max-width: 600px;
                margin: 0 auto;
                background-color: #ffffff;
            }

            .content {
                padding: 20px;
                color: #333333;
                line-height: 1.5;
            }

            .button {
                background-color: #2472c4;
                color: #ffffff;
                padding: 12px 25px;
                text-decoration: none;
                border-radius: 5px;
                display: inline-block;
                font-weight: bold;
            }

            .footer {
                padding: 20px;
                text-align: center;
                font-size: 12px;
                color: #999999;
            }
        </style>
    </head>

    <body>
        <table role="presentation" class="container">
            <tr>
                <td class="content">
                    <h1 style="margin-top: 0;">Восстановление пароля к своему аккаунту</h1>
                    <p>Чтобы установить новый пароль, пройдите по ссылке:</p>

                    <a class="button" href="{{ $reg_link }}">
                        Восстановить пароль
                    </a>

                    <p><b>Если это были не вы, просто проигнорируйте это письмо</b></p>

                    <p>Ссылка действительна в течение 24 часов.</p>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    &copy; <a href="{{ url() }}">{{ env('APP_FULLNAME') }}</a>
                </td>
            </tr>
        </table>
    </body>

</html>
