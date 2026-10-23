<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request, int $id = 0)
    {
        if ($id === 0) {
            return abort(404);
        }

        $user = $request->user();

        if (! $user || (int) $user->id !== $id) {
            return abort(404);
        }

        $userName = $user->name;

        $seoTitle = "Панель управления {$userName}";
        $seoDescription = "Панель управления {$userName}" . ' ' . config('app.desc');

        $data = [
            'user_id' => $user->id,
            'user_name' => $userName,
            'translation' => $request->cookie('translation', 'rbo'),
            'book_num' => $request->cookie('book_num', 1),
            'chapter_num' => $request->cookie('chapter_num', 1),
            'seo_title' => $seoTitle,
            'seo_description' => $seoDescription,
            'theme' => $_COOKIE['apptheme'] ?? '',
        ];

        return view('user.dashboard', $data);
    }

    public function editUser(Request $request, int $id = 0)
    {
        if ($id === 0) {
            return abort(404);
        }

        $user = $request->user();

        if (! $user || (int) $user->id !== $id) {
            return abort(404);
        }

        $seoTitle = "Обновить {$user->name}";
        $seoDescription = "Панель управления {$user->name}" . ' ' . config('app.desc');

        $data = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'translation' => $request->cookie('translation', 'rbo'),
            'book_num' => $request->cookie('book_num', 1),
            'chapter_num' => $request->cookie('chapter_num', 1),
            'seo_title' => $seoTitle,
            'seo_description' => $seoDescription,
            'theme' => $_COOKIE['apptheme'] ?? '',
        ];

        return view('user.edit', $data);
    }

    public function updateUser(Request $request, int $id = 0)
    {
        if ($id === 0) {
            return abort(404);
        }

        $user = $request->user();

        if (! $user || (int) $user->id !== $id) {
            return abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
        ]);

        $name = trim(strip_tags($validated['name']));

        if ($name === '') {
            return back()
                ->withErrors(['name' => 'Имя не может быть пустым.'])
                ->withInput();
        }

        if ($name === $user->name) {
            return back()
                ->withErrors(['name' => 'Ваше имя осталось прежним.'])
                ->withInput();
        }

        try {
            $user->name = $name;
            $user->save();
        } catch (QueryException $exception) {
            report($exception);

            return back()
                ->withErrors(['name' => 'Это имя уже занято. Выберите другое.'])
                ->withInput();
        }

        return redirect()
            ->route('dashboard', ['id' => $user->id])
            ->with('status', "Вы обновили свое имя, {$user->name}");
    }
}
