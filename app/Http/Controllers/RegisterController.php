<?php

namespace App\Http\Controllers;

use App\Mail\SendRegisterLink;
use App\Mail\SendRestoreLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class RegisterController extends Controller
{
    public function index(Request $request)
    {
        $emailKeyInput = $request->input('email_key', '');
        $emailAddressInput = $request->input('email_address', '');
        $emailKey = is_string($emailKeyInput) ? $emailKeyInput : '';
        $emailAddress = is_string($emailAddressInput) ? $emailAddressInput : '';

        $data = [
            'seo_title' => '',
            'seo_description' => '',
            'email_key' => '',
            'email_address' => '',
            'theme' => $_COOKIE['apptheme'] ?? '',
            'book_num' => $request->cookie('book_num', 1),
            'chapter_num' => $request->cookie('chapter_num', 1),
        ];

        if ($emailAddress !== '' && $emailKey !== '' && filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $emailAddress)->first();

            if ($user && $this->hasValidRegistrationKey($user, $emailKey)) {
                $data['email_key'] = $emailKey;
                $data['email_address'] = $emailAddress;
            }
        }

        return view('auth.register', $data);
    }

    public function restore(Request $request)
    {
        $data = [
            'seo_title' => 'Страница восстановления пароля' . ' - ' . config('app.fullname'),
            'seo_description' => 'Страница восстановления пароля' . ' - ' . config('app.desc'),
            'theme' => $_COOKIE['apptheme'] ?? '',
            'book_num' => $request->cookie('book_num', 1),
            'chapter_num' => $request->cookie('chapter_num', 1),
        ];

        return view('auth.restore', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'password-confirm' => ['required', 'string', 'same:password'],
            'email_key' => ['required', 'string', 'max:255'],
            'privacy' => ['accepted'],
        ]);

        $email = mb_strtolower(trim($validated['email']));
        $user = User::where('email', $email)->first();

        if (! $user || ! $this->hasValidRegistrationKey($user, $validated['email_key'])) {
            return back()
                ->withErrors([
                    'email' => 'Ссылка подтверждения недействительна или истекла. Запросите новую ссылку.',
                ])
                ->withInput();
        }

        try {
            $user->name = $this->makeUniqueName($email, (int) $user->id);
            $user->password = Hash::make($validated['password']);
            $user->email_key = '';
            $user->email_key_expires_at = null;
            $user->email_verified_at = now();
            $user->save();
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withErrors([
                    'password' => 'Не удалось сохранить пользователя. Попробуйте ещё раз.',
                ])
                ->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard', ['id' => $user->id])
            ->with('status', "Добро пожаловать в свой аккаунт, {$user->name}");
    }

    /**
     * This is AJAX HTMX Method.
     */
    public function sendRegisterLink(Request $request)
    {
        $emailInput = $request->input('email');
        $emailAddress = mb_strtolower(trim(is_string($emailInput) ? $emailInput : ''));
        $restoreInput = $request->input('_restore');
        $isRestore = is_scalar($restoreInput) && (int) $restoreInput === 1;

        if (! filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            return $this->message('error', 'Убедитесь, что адрес почты введен корректно!');
        }

        $emailKey = Str::random(64);
        $emailKeyHash = hash('sha256', $emailKey);

        if (! $isRestore) {
            if (User::where('email', $emailAddress)->exists()) {
                return $this->message(
                    'error',
                    "Адрес {$emailAddress} уже зарегистрирован в системе. Если это ваш, воспользуйтесь формой восстановления пароля."
                );
            }

            try {
                $user = User::create([
                    'name' => $this->makeUniqueName($emailAddress),
                    'email' => $emailAddress,
                    'password' => Hash::make(Str::random(40)),
                    'email_key' => $emailKeyHash,
                    'email_key_expires_at' => now()->addHour(),
                ]);
            } catch (Throwable $exception) {
                report($exception);

                return $this->message('error', 'Не удалось создать аккаунт. Попробуйте ещё раз позже.');
            }

            $regLink = route('registration', [
                'email_address' => $emailAddress,
                'email_key' => $emailKey,
            ]);

            try {
                Mail::to($emailAddress)->send(new SendRegisterLink($regLink));
            } catch (Throwable $exception) {
                report($exception);

                return $this->message('error', 'Не удалось отправить письмо. Попробуйте ещё раз позже.');
            }

            return $this->message(
                'success',
                "Проверьте ваш почтовый ящик {$emailAddress}, должно прийти письмо с ссылкой для установки пароля."
            );
        }

        $user = User::where('email', $emailAddress)->first();

        if (! $user) {
            return $this->message(
                'error',
                "Адрес {$emailAddress} не зарегистрирован в системе. Попробуйте зарегистрироваться."
            );
        }

        $user->email_key = $emailKeyHash;
        $user->email_key_expires_at = now()->addHour();
        $user->save();

        $regLink = route('registration', [
            'email_address' => $emailAddress,
            'email_key' => $emailKey,
        ]);

        try {
            Mail::to($emailAddress)->send(new SendRestoreLink($regLink));
        } catch (Throwable $exception) {
            report($exception);

            return $this->message('error', 'Не удалось отправить письмо. Попробуйте ещё раз позже.');
        }

        return $this->message(
            'success',
            "Проверьте ваш почтовый ящик {$emailAddress}, должно прийти письмо с ссылкой для установки пароля."
        );
    }

    public function initRestore(Request $request)
    {
        $request->merge(['_restore' => 1]);

        return $this->sendRegisterLink($request);
    }

    private function hasValidRegistrationKey(User $user, string $emailKey): bool
    {
        if ((string) $user->email_key === '' || $user->email_key_expires_at === null) {
            return false;
        }

        if ($user->email_key_expires_at->isPast()) {
            return false;
        }

        return hash_equals((string) $user->email_key, hash('sha256', $emailKey));
    }

    private function makeUniqueName(string $email, int $exceptUserId = 0): string
    {
        $base = mb_ucfirst(mb_substr(explode('@', $email)[0], 0, 220));
        $base = trim(strip_tags($base));

        if ($base === '') {
            $base = 'Пользователь';
        }

        $name = $base;
        $suffix = 2;

        while (User::where('name', $name)->where('id', '!=', $exceptUserId)->exists()) {
            $name = mb_substr($base, 0, 240) . $suffix;
            $suffix++;
        }

        return $name;
    }

    private function message(string $type, string $text): \Illuminate\Http\Response
    {
        $class = $type === 'error' ? 'message-error' : 'message-success';

        return response(
            '<div class="message ' . $class . '">' . e($text) . '</div>'
        );
    }
}
