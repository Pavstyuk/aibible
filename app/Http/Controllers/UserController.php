<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request, int $id = 0)
    {

        if (empty($id) || $id === 0) {
            return abort(404);
        }

        $user_id = $request->user()->id;

        if (empty($user_id)) {
            return abort(404);
        }

        if ($user_id && $id !== $user_id) {
            return abort(404);
        }

        $user_name = $request->user()->name;

        $seo_title = "Панель управления $user_name";
        $seo_description = "Панель управления $user_name" . ' ' . env('APP_DESC');

        $data = [
            'user_id'   => $user_id,
            'user_name' => $user_name,
            'translation' => $request->cookie('translation', 'rbo'),
            'book_num' => $request->cookie('book_num', 1),
            'chapter_num' => $request->cookie('chapter_num', 1),
            'seo_title' => $seo_title,
            'seo_description' => $seo_description,
            'theme' => $_COOKIE['apptheme'] ?? '',
        ];

        $template = 'user.dashboard';

        return view($template, $data);
    }

    function editUser(Request $request, int $id = 0)
    {
        if (empty($id) || $id === 0) {
            return abort(404);
        }

        $user_id = $request->user()->id;

        if (empty($user_id)) {
            return abort(404);
        }

        if ($user_id && $id !== $user_id) {
            return abort(404);
        }

        $user_name = $request->user()->name;
        $user_email = $request->user()->email;

        $seo_title = "Обновить $user_name";
        $seo_description = "Панель управления $user_name" . ' ' . env('APP_DESC');

        $data = [
            'user_id'   => $user_id,
            'user_name' => $user_name,
            'user_email' => $user_email,
            'translation' => $request->cookie('translation', 'rbo'),
            'book_num' => $request->cookie('book_num', 1),
            'chapter_num' => $request->cookie('chapter_num', 1),
            'seo_title' => $seo_title,
            'seo_description' => $seo_description,
            'theme' => $_COOKIE['apptheme'] ?? '',
        ];

        $template = 'user.edit';

        return view($template, $data);
    }

    function updateUser(Request $request, int $id = 0)
    {
        if (empty($id) || $id === 0) {
            return abort(404);
        }

        $user_id = $request->user()->id;

        if (empty($user_id)) {
            return abort(404);
        }

        if ($user_id && $id !== $user_id) {
            return abort(404);
        }

        $name = htmlspecialchars($request->input('name'));
        $email = htmlspecialchars($request->input('email'));

        $user = User::where('email', $email)->first();

        if ($user->name === $name) {
            return back()->withErrors([
                'name' => "Ваше имя осталось прежним: <i>$user->name</i>",
            ])->withInput();
        }


        if ($user) {
            try {
                $user->name = $name;
                $user->save();
            } catch (Exception $e) {
                return back()->withErrors([
                    'name' => "Ошибка сохранения имени. Имя <i>$user->name</i> уже занято",
                ])->withInput();
            }
        } else {
            return back()->withErrors([
                'email' => "Странно не могу найти пользователя с email <i>$user->email</i>",
            ])->withInput();
        }


        if ($user->wasChanged()) {
            Auth::login($user);
            return redirect()
                ->route('dashboard', ['id' => $user->id])
                ->with('status', "Вы обновили свое имя, $user->name");
        } else {
            return back()->withErrors([
                'name' => "Ошибка сохранения имени. Имя <i>$user->name</i> уже занято",
            ])->withInput();
        }
    }
}
