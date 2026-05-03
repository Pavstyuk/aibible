<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    function page() {}

    function privacy(Request $request)
    {
        $seo_title = 'Политика в отношении обработки персональных данных. ' . env('APP_FULLNAME');
        $seo_description = 'Политика в отношении обработки персональных данных. ' . env('APP_DESC');
        $data = [
            'user_id'           => auth()->id() ?? 0,
            'book_num'          => $request->cookie('book_num'),
            'chapter_num'       => $request->cookie('chapter_num'),
            'seo_title'         => $seo_title,
            'seo_description'   => $seo_description,
            'theme'             => $_COOKIE['apptheme'] ?? '',
        ];

        $template = 'pages.privacy';

        return view($template, $data);
    }
}
