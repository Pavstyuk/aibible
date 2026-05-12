<?php

namespace App\Http\Controllers;

use App\Mail\SendRegisterLink;
use App\Mail\SendRestoreLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    function index(Request $request)
    {
        $email_key = htmlspecialchars($request->input('email_key', ''));
        $email_address = htmlspecialchars($request->input('email_address', ''));

        $data = [
            'seo_title' => '',
            'seo_description' => '',
            'email_key' => '',
            'email_address' => '',
            'theme' => $_COOKIE['apptheme'] ?? '',
            'book_num' => $request->cookie('book_num', 1),
            'chapter_num' => $request->cookie('chapter_num', 1),
        ];

        if ($email_address && $email_key && filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $email_address)->first();
            if ($user && $user->email_key === $email_key) {
                $data['email_key'] = $email_key;
                $data['email_address'] = $email_address;
            }
        }

        return view('auth.register', $data);
    }

    function restore(Request $request)
    {
        $data = [
            'seo_title' => 'Страница восстановления пароля' . ' - ' . env('APP_FULLNAME'),
            'seo_description' => 'Страница восстановления пароля' . ' - ' . env('APP_DESC'),
            'theme' => $_COOKIE['apptheme'] ?? '',
            'book_num' => $request->cookie('book_num', 1),
            'chapter_num' => $request->cookie('chapter_num', 1),
        ];
        return view('auth.restore', $data);
    }

    function store(Request $request)
    {

        $email = htmlspecialchars(trim($request->input('email')));

        $name = mb_ucfirst(explode('@', $email)[0]);

        if ($request->input('password') === $request->input('password-confirm')) {
            $password = $request->input('password');
        } else {
            return back()->withErrors([
                'password-confirm' => 'Ошибка подтверждения пароля',
            ])->withInput();
        }

        $privacy = $request->input('privacy');

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'name' => $name,
                'password' => Hash::make($password),
                'email_key' => null
            ]);
        }
        if ($user->wasChanged()) {

            // Текст уведомления
            $messageText = <<<TEXT
                На сайте aibible.ru зарегистрирован новый пользователь. \r\n
                Email пользователя: $email.
            TEXT;

            // Отправка письма
            Mail::raw($messageText, function ($message) {
                $message->to(env('EMAIL_ADMIN'))
                    ->subject('👋 Новый пользователь на сайте Библия ИИ');
            })->queue();


            // Аутентификация пользователя
            Auth::login($user);
            return redirect()
                ->route('dashboard', ['id' => $user->id])
                ->with('status', "Добро пожаловать в свой аккаунт, $user->name");
        } else {
            return back()->withErrors([
                'password' => 'Ошибка сохранения пользователя в базе данных',
            ])->withInput();
        }
    }

    /**
     * This is AJAX HTMX Method
     */
    function sendRegisterLink(Request $request)
    {
        $email_address = htmlspecialchars($request->input('email'));
        $key = htmlspecialchars($request->input('_key', ''));
        if (!empty($request->input('_restore')) && (int) $request->input('_restore') === 1) {
            $is_restore = true;
        } else {
            $is_restore = false;
        }


        $verification_key = 'aibible_form_key_' . substr((string) time(), 0, 8);
        if (empty($key) || $key !== $verification_key) {
            die("Стоп Спам");
        }

        if (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
            echo <<<HTML
            <div class="message message-error">
                Убедитесь, что адрес почты введен корректно!
            </div>
            HTML;
            exit;
        }

        if (!$is_restore) {

            if (User::where('email', $email_address)->exists()) {
                echo <<<HTML
                    <div class="message message-error">
                        Адрес $email_address уже зарегистрирован в системе. Если это ваш, воспользуйтесь формой <a class="text-color-second" href='/restore'>восстановления пароля</a>
                    </div>
                HTML;
                exit;
            }

            $email_key = Str::uuid()->toString();

            $user = User::create([
                'email'     => $email_address,
                'email_key' => $email_key,
            ]);

            $reg_link = route(
                'registration',
                [
                    'email_address' => $email_address,
                    'email_key' => $email_key
                ]
            );
            Mail::to($email_address)->send(new SendRegisterLink($reg_link));
        }

        if ($is_restore) {
            if (!User::where('email', $email_address)->exists()) {
                echo <<<HTML
                    <div class="message message-error">
                        Адрес $email_address НЕ зарегистрирован в системе. Попробуйте <a class="text-color-second" href='/register'>зарегистрироваться</a>
                    </div>
                HTML;
                exit;
            }

            $email_key = Str::uuid()->toString();

            $user = User::where('email', $email_address)->first();

            if ($user) {
                $user->update(['email_key' => $email_key]);
                $user->save();
            }

            $reg_link = route(
                'registration',
                [
                    'email_address' => $email_address,
                    'email_key' => $email_key
                ]
            );
            Mail::to($email_address)->send(new SendRestoreLink($reg_link));
        }


        echo <<<HTML
            <div class="message message-success">
                Проверьте ваш почтовый ящик $email_address, должно прийти письмо с ссылкой для установки пароля.
            </div>
            HTML;
        exit;
    }
}
