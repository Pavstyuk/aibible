<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    function index(Request $request)
    {
        $religion = session('religion');

        $all = session()->all();

        // dd($all);

        $data = [
            'seo_title' => '',
            'seo_description' => '',
            'theme' => $_COOKIE['apptheme'] ?? '' ?? '',
            'book_num' => $request->cookie('book_num', 1),
            'chapter_num' => $request->cookie('chapter_num', 1),
        ];
        return view('auth.login', $data);
    }

    public function store(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $remember = $request->input('remember');

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user_id = Auth::id();
            $user_name = Auth::user()->name;
            $data = [
                'user_id'           => $user_id,
                'user_name'         => $user_name,
                'seo_title'         => '',
                'seo_description'   => '',
                'theme'             => $_COOKIE['apptheme'] ?? '' ?? '',
                'book_num'          => $request->cookie('book_num', 1),
                'chapter_num'       => $request->cookie('chapter_num', 1),
            ];
            return redirect()
                ->route('dashboard', ['id' => $user_id])
                ->with('status', "Добро пожаловать в свой аккаунт, $user_name");
        }

        return back()->withErrors(
            ['email' => 'Неверный данные для входа']
        )->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken(); // Critical for CSRF security

        return redirect('/');
    }
}
