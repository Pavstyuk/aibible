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
        $searchInput = $request->input('searching', '');
        $searchQueryRaw = trim(is_string($searchInput) ? $searchInput : '');
        $searchQueryRaw = mb_substr($searchQueryRaw, 0, 255);
        $searchQuery = $searchQueryRaw;

        $translationInput = $request->input('search-translation', 'rbo');
        $searchTranslation = $this->clearTranslationParameter(
            is_string($translationInput) ? $translationInput : 'rbo'
        );

        if (! $this->isValidTranslation($searchTranslation)) {
            $searchTranslation = 'rbo';
        }

        $bookInput = $request->input('search-book', '');
        $searchBook = $this->clearIntParameter(
            is_string($bookInput) ? $bookInput : ''
        );

        if ($searchBook < 1 || $searchBook > 66) {
            $searchBook = 0;
        }

        $searchResults = [];

        if ($searchQueryRaw !== '') {
            $query = DB::table($searchTranslation);

            if ($searchBook > 0) {
                $query->where('book_num', $searchBook);
            }

            $searchResults = $query
                ->whereLike('verse', '%' . $searchQueryRaw . '%', caseSensitive: false)
                ->get();
        }

        $translations = DB::table('bibles')->get();
        $bibles = [];

        foreach ($translations as $item) {
            if (in_array($item->translation_slug, config('bible.translations', []), true)) {
                $bibles[$item->translation_slug] = $item->translation_name;
            }
        }

        $booksList = DB::table($searchTranslation)->pluck('book_name', 'book_num');

        $data = [
            'search_results' => $searchResults,
            'search_query' => $searchQuery,
            'search_translation' => $searchTranslation,
            'search_book' => $searchBook,
            'bibles' => $bibles,
            'books_list' => $booksList,
            'seo_title' => __('Поиск по Библии.') . ' | ' . config('app.fullname'),
            'seo_description' => __('Поиск по Библии') . ' | ' . config('app.desc'),
            'theme' => $_COOKIE['apptheme'] ?? '',
            'book_num' => $request->cookie('book_num', 1),
            'chapter_num' => $request->cookie('chapter_num', 1),
        ];

        return view('bible.search', $data);
    }
}
