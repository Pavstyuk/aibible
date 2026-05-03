<?php

namespace App\Http\Controllers;

use App\Traits\SanitizeTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    use SanitizeTrait;

    public function search(Request $request)
    {
        $search_query = htmlspecialchars($request->input('searching')) ?? '';
        $search_translation = $this->clearTranslationParameter($request->input('search-translation', 'rbo'));
        $search_book = $request->input('search-book') ? $this->clearIntParameter($request->input('search-book')) : '';



        // $search_results = DB::table($search_translation)
        //     ->where($args)
        //     ->whereFullText('verse', $search_query)->get();

        $search_results = [];
        if ($search_query) {
            if ($search_book) {
                $args = ['book_num' => $search_book];
                $search_results = DB::table($search_translation)
                    ->where($args)
                    ->whereLike('verse', "%$search_query%", caseSensitive: false)
                    ->get();
            } else {
                $search_results = DB::table($search_translation)
                    ->whereLike('verse', "%$search_query%", caseSensitive: false)
                    ->get();
            }
        }

        $translations = DB::table('bibles')->get();

        if ($translations->count() > 0) {
            $bibles = [];
            foreach ($translations as $item) {
                $bibles[$item->translation_slug] = $item->translation_name;
            }
        } else {
            $bibles = [];
        }

        $books_list = DB::table($search_translation)->pluck('book_name', 'book_num');


        $data = [
            'search_results' => $search_results,
            'search_query' => $search_query,
            'search_translation' => $search_translation,
            'search_book' => $search_book,
            'bibles' => $bibles,
            'books_list' => $books_list,
            'seo_title' => __('Поиск по Библии.') . ' | ' . env('APP_FULLNAME'),
            'seo_description' => __('Поиск по Библии') . ' | ' . env('APP_DESCRIPTION'),
            'theme' => $_COOKIE['apptheme'] ?? '' ?? '',
            'book_num' => $request->cookie('book_num', 1),
            'chapter_num' => $request->cookie('chapter_num', 1),
        ];

        return view('bible.search', $data);
    }
}
