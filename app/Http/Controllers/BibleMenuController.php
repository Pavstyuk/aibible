<?php

namespace App\Http\Controllers;

use App\Traits\SanitizeTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BibleMenuController extends Controller
{
    use SanitizeTrait;

    public array $chapters = [];

    private ?string $chaptersTranslation = null;

    public function getChaptersCount(string $translation): array
    {

        if (!$this->isValidTranslation($translation)) {
            return [];
        }

        if ($this->chaptersTranslation === $translation) {
            return $this->chapters;
        }

        $this->chapters = DB::table($translation)
            ->selectRaw('book_num, COUNT(DISTINCT chapter_num) AS chapters_count')
            ->groupBy('book_num')
            ->orderBy('book_num')
            ->pluck('chapters_count', 'book_num')
            ->map(fn($count) => (int) $count)
            ->all();

        $this->chaptersTranslation = $translation;

        return $this->chapters;
    }

    public function createBookNav(string $current_trans)
    {
        if (!in_array($current_trans, config('bible.translations', []), true)) {
            return [];
        }

        $book_names = DB::table($current_trans)->pluck('book_name', 'book_num');
        $books_menu = [];
        foreach ($book_names as $b => $name) {
            $books_menu["$name"] = "/$current_trans/$b";
        }
        return $books_menu;
    }

    public function createChapterNav(string $current_trans, int $current_book)
    {
        $chapter_count = $this->chapters[$current_book] ?? 1;
        $chapters_menu = [];
        for ($c = 1; $c <= $chapter_count; $c++) {
            $chapters_menu[$c] = "/$current_trans/$current_book/$c";
        }

        return $chapters_menu;
    }

    public function books(Request $request)
    {
        $translation = $this->normalizeTranslation($request->cookie('translation', 'rbo'));
        $book_num = $this->normalizeBookNumber($request->cookie('book_num', 1));

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

        $translation = $this->normalizeTranslation($request->cookie('translation', 'rbo'));
        $book_num = $this->normalizeBookNumber($request->cookie('book_num', 1));
        $chapter_num = max(1, (int) $request->cookie('chapter_num', 1));


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

        $translation = $this->normalizeTranslation($request->cookie('translation', 'rbo'));
        $book_num = $this->normalizeBookNumber($request->cookie('book_num', 1));
        $chapter_num = max(1, (int) $request->cookie('chapter_num', 1));

        $translations = DB::table('bibles')->get();

        if ($translations->count() === 0) {
            return __('Translation not found');
        }

        $list = [];
        foreach ($translations as $item) {
            if (!in_array($item->translation_slug, config('bible.translations', []), true)) {
                continue;
            }

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

    private function normalizeTranslation(string $translation): string
    {
        $translation = strtolower((string) preg_replace('/[^a-z]/', '', $translation));

        return in_array($translation, config('bible.translations', []), true)
            ? $translation
            : 'rbo';
    }

    private function normalizeBookNumber(string|int $bookNum): int
    {
        $bookNum = (int) $bookNum;

        return ($bookNum >= 1 && $bookNum <= 66) ? $bookNum : 1;
    }
}
