<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    function page() {}

    function policy(Request $request)
    {
        $seo_title =
            "Политика в отношении обработки персональных данных. " .
            config("app.fullname");
        $seo_description =
            "Политика в отношении обработки персональных данных. " .
            config("app.desc");
        $data = [
            "user_id" => auth()->id() ?? 0,
            "book_num" => $request->cookie("book_num"),
            "chapter_num" => $request->cookie("chapter_num"),
            "seo_title" => $seo_title,
            "seo_description" => $seo_description,
            "theme" => $_COOKIE["apptheme"] ?? "",
        ];

        $template = "pages.policy";

        return view($template, $data);
    }

    function privacy(Request $request)
    {
        $seo_title =
            "Согласие на обработку персональных данных. " .
            config("app.fullname");
        $seo_description =
            "Согласие на обработку персональных данных. " . config("app.desc");
        $data = [
            "user_id" => auth()->id() ?? 0,
            "book_num" => $request->cookie("book_num"),
            "chapter_num" => $request->cookie("chapter_num"),
            "seo_title" => $seo_title,
            "seo_description" => $seo_description,
            "theme" => $_COOKIE["apptheme"] ?? "",
        ];

        $template = "pages.privacy";

        return view($template, $data);
    }

    function apiPage(Request $request)
    {

        $data = [
            "user_id" => auth()->id() ?? 0,
            "book_num" => $request->cookie("book_num"),
            "chapter_num" => $request->cookie("chapter_num"),
            "theme" => $_COOKIE["apptheme"] ?? "",
            'seo_title' => 'Библия. Быстрый API сервис на Rust',
            'seo_description' => ' Простой HTTP-сервер для чтения Библии и поиска по тексту. Написан на Rust без веб-фреймворков: используется стандартный TcpListener, JSON-файлы с текстом и кэш в памяти.'
        ];


        $template = "pages.api";
        return view($template, $data);
    }
}
