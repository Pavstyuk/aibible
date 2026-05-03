<?php

namespace App\Http\Controllers;

use App\Traits\SanitizeTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BibleMenuController extends Controller
{
    use SanitizeTrait;
    public array $chapters = [1 => 50, 2 => 40, 3 => 27, 36, 34, 24, 21, 4, 31, 24, 22, 25, 29, 36, 10, 13, 10, 42, 150, 31, 12, 8, 66, 52, 5, 48, 12, 14, 3, 9, 1, 4, 7, 3, 3, 3, 2, 14, 4, 28, 16, 24, 21, 28, 5, 5, 3, 5, 1, 1, 1, 16, 16, 13, 6, 6, 4, 4, 5, 3, 6, 4, 3, 1, 13, 22];

    public function createBookNav(string $current_trans)
    {
        $book_names = DB::table($current_trans)->pluck('book_name', 'book_num');
        $books_menu = [];
        foreach ($book_names as $b => $name) {
            $books_menu["$name"] = "/$current_trans/$b";
        }
        return $books_menu;
    }

    public function createChapterNav(string $current_trans, int $current_book)
    {
        $chapter_count = $this->chapters[$current_book];
        $chapters_menu = [];
        for ($c = 1; $c <= $chapter_count; $c++) {
            $chapters_menu[$c] = "/$current_trans/$current_book/$c";
        }

        return $chapters_menu;
    }

    public function books(Request $request)
    {
        $translation = $request->cookie('translation', 'rbo');
        $book_num = $request->cookie('book_num', 1);

        $book_names = DB::table($translation)->pluck('book_name', 'book_num');
        $book_name = $book_names[$book_num] ?? '';

        if (!$book_name) return __('Book name not found');

        $books_testaments = $this->createBookNav($translation);

        $data = [
            'book' => $book_name,
            'books_old' => array_slice($this->createBookNav($translation), 0, 39),
            'books_new' => array_slice($this->createBookNav($translation), 39, 27),
        ];

        return view('partials.menu-books', $data);
    }

    public function chapters(Request $request)
    {

        $translation = $request->cookie('translation', 'rbo');
        $book_num = $request->cookie('book_num', 1);
        $chapter_num = $request->cookie('chapter_num', 1);


        $book_names = DB::table($translation)->pluck('book_name', 'book_num');
        $book_name = $book_names[$book_num] ?? '';

        if (!$book_name) return __('Book name not found');

        $data = [
            'book' => $book_name,
            'chapter' => $chapter_num,
            'chapters_nav' => $this->createChapterNav($translation, $book_num),
        ];

        return view('partials.menu-chapters', $data);
    }

    public function translations(Request $request)
    {

        $translation = $request->cookie('translation', 'rbo');
        $book_num = $request->cookie('book_num', 1);
        $chapter_num = $request->cookie('chapter_num', 1);

        $translations = DB::table('bibles')->get();

        if ($translations->count() === 0) {
            return __('Translation not found');
        }

        $list = [];
        foreach ($translations as $item) {
            $list[] = [
                'slug' => $item->translation_slug,
                'name' => $item->translation_name,
                'year' => $item->translation_year,
            ];
        }

        // dd($translations_list);
        return view(
            'partials.menu-translations',
            [
                'list'          => $list,
                'translation'   => $translation,
                'book_num'      => $book_num,
                'chapter_num'   => $chapter_num,
            ]
        );
    }
}
