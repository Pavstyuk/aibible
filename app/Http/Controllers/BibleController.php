<?php

namespace App\Http\Controllers;

use App\Traits\SanitizeTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AIController;
use App\Models\Favorite;

class BibleController extends Controller
{
    use SanitizeTrait;

    public array $chapters = [1 => 50, 2 => 40, 3 => 27, 36, 34, 24, 21, 4, 31, 24, 22, 25, 29, 36, 10, 13, 10, 42, 150, 31, 12, 8, 66, 52, 5, 48, 12, 14, 3, 9, 1, 4, 7, 3, 3, 3, 2, 14, 4, 28, 16, 24, 21, 28, 5, 5, 3, 5, 1, 1, 1, 16, 16, 13, 6, 6, 4, 4, 5, 3, 6, 4, 3, 1, 13, 22];

    public array $translations = ['rbo', 'rst', 'nasb'];

    public function buildVerses(string $param): array
    {
        $verse_numbers = [];
        $nums = explode('-', $param);
        if (count($nums) > 1) {
            for ($v = $nums[0]; $v <= $nums[1]; $v++) {
                $verse_numbers[] = $this->clearIntParameter($v);
            }
        } else {
            $verse_numbers[] = $this->clearIntParameter($param);
        }
        return $verse_numbers;
    }

    public function getWordsForTitle(string $text, int $count = 3): string
    {
        $title = '';
        $words = explode(' ', $text);
        for ($w = 0; $w <= $count; $w++) {
            $w !== $count ? $sep = ' ' : $sep = '';
            if (isset($words[$w])) {
                $title .= $words[$w] . $sep;
            }
        }
        $title_clear = trim($title, " ,.;:\r\n");
        if (count($words) > $count) {
            $title_clear .= '...';
        }
        return $title_clear;
    }

    public function createNextLink(string $current_trans, int $current_book, int $current_chapter): string
    {

        if ($current_book === 66 && $current_chapter === 22) return '';

        if ($current_chapter < $this->chapters[$current_book]) {
            $current_chapter++;
        } else {
            $current_book++;
            $current_chapter = 1;
        }
        $next_link = "/$current_trans/$current_book/$current_chapter";
        return $next_link;
    }

    public function createPrevLink(string $current_trans, int $current_book, int $current_chapter): string
    {
        if ($current_book === 1 && $current_chapter === 1) return '';

        if ($current_chapter !== 1) {
            $current_chapter--;
        } else {
            $current_book--;
            $current_chapter = $this->chapters[$current_book];
        }
        $prev_link = "/$current_trans/$current_book/$current_chapter";
        return $prev_link;
    }

    public function createVerseLink(string $current_trans, int $verse_id): string
    {
        $verse = DB::table($current_trans)->find($verse_id);

        if ($verse) {
            $verse_link = "/$current_trans/{$verse->book_num}/{$verse->chapter_num}/{$verse->verse_num}";
        } else {
            $verse_link = '';
        }

        return $verse_link;
    }

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

    /**
     * Display a full Bible Chapter.
     */

    public function index(Request $request, string $translation = '', string $book_num = '', string $chapter_num = '')
    {

        if ($translation === '') {
            $translation = $request->cookie('translation', 'rbo');
        }

        if (!in_array($translation, $this->translations)) {
            return abort(404);
        }

        if ($book_num === '') {
            $book_num = $request->cookie('book_num', 1);
        }
        if ($chapter_num === '') {
            $chapter_num = $request->cookie('chapter_num', 1);
        }
        $translation = $this->clearTranslationParameter($translation);
        $book_num = $this->clearIntParameter($book_num);
        $chapter_num = $this->clearIntParameter($chapter_num);

        $marked_arr = [];
        if ($request->input('marked')) {
            $marked_arr = $this->buildVerses($request->input('marked'));
        }


        $args = [
            'book_num'      => $book_num,
            'chapter_num'   => $chapter_num
        ];

        $text = DB::table($translation)
            ->where($args)
            ->get();


        if ($text->count() === 0) {
            $args = [
                'book_num'      => $book_num,
                'chapter_num'   => 1
            ];

            $chapter_num = 1;

            $text = DB::table($translation)
                ->where($args)
                ->get();
        }

        if ($text->count() === 0) return abort(404);

        Cookie::queue('translation', $translation, 43200);
        Cookie::queue('book_num', $book_num, 43200);
        Cookie::queue('chapter_num', $chapter_num, 43200);

        $book_title = $text[0]->book_name;

        $next_link = $this->createNextLink($translation, $book_num, $chapter_num);
        $prev_link = $this->createPrevLink($translation, $book_num, $chapter_num);
        $books_nav = $this->createBookNav($translation);
        $chapter_nav = $this->createChapterNav($translation, $book_num);

        $seo_title = "$book_title глава $chapter_num, " . strtoupper($translation) . ' | ' . env('APP_FULLNAME');
        $seo_description = "$book_title глава $chapter_num, " . strtoupper($translation) . ' | ' . env('APP_DESC');

        $data = [
            'translation' => $translation,
            'book_num' => $book_num,
            'book' => $book_title,
            'chapter_num' => $chapter_num,
            'text' => $text,
            'marked_arr' => $marked_arr,
            'next_link' => $next_link,
            'prev_link' => $prev_link,
            'books_nav' => $books_nav,
            'chapters_nav' => $chapter_nav,
            'seo_title' => $seo_title,
            'seo_description' => $seo_description,
            'theme' => $_COOKIE['apptheme'] ?? '',
        ];

        $template = 'bible.chapter';

        return view($template, $data);
    }

    /**
     * Display a Single Verse or Multi Verse Page
     */

    public function single(Request $request, string $translation, string $book_num, string $chapter_num,  string $verse_num)
    {

        $translation = $this->clearTranslationParameter($translation);
        $book_num = $this->clearIntParameter($book_num);
        $chapter_num = $this->clearIntParameter($chapter_num);
        $verse_numbers = $this->buildVerses($verse_num);

        if (count($verse_numbers) > 1) {
            $verse_display = $verse_numbers[0] . '-' . end($verse_numbers);
        } else {
            $verse_display = $verse_numbers[0];
        }

        $args = [
            'book_num'      => $book_num,
            'chapter_num'   => $chapter_num
        ];

        $text = DB::table($translation)
            ->where($args)
            ->whereIn('verse_num', $verse_numbers)
            ->get();

        if ($text->count() === 0) return abort(404);

        $verse_id = $text->first()->id;
        $verse_id_end = $text->last()->id;

        $next_id = ++$text->last()->id;

        $prev_id = --$text->first()->id;

        $next_verse_link = $this->createVerseLink(
            $translation,
            $next_id
        );

        $prev_verse_link = $this->createVerseLink(
            $translation,
            --$prev_id
        );

        $book_title = $text[0]->book_name;

        $single_title = $this->getWordsForTitle($text[0]->verse);

        $seo_title = "$single_title $book_title $chapter_num:$verse_display" . ' — ' . env('APP_FULLNAME');
        $seo_description = "$single_title $book_title $chapter_num:$verse_display " . strtoupper($translation) . '. ' . env('APP_DESC');

        $ai = new AIController();
        $comments = $ai->getCommentsByVerseID($translation, $verse_id);

        $is_favorite = false;

        if (!empty($request->user()->id)) {
            $user_id = $request->user()->id;
            if ($verse_id === $verse_id_end) {
                $ids = (string) $verse_id_end;
            } else {
                $ids = "$verse_id-$verse_id_end";
            }
            $favorite = new FavoriteController;
            $is_favorite = $favorite->isFavorite($user_id, $translation, $ids);
        } else {
            $user_id = 0;
        }

        $data = [
            'user_id'       => $user_id,
            'translation'   => $translation,
            'verse_id'      => $verse_id,
            'verse_id_end'  => $verse_id_end,
            'is_favorite'   => $is_favorite,
            'book_num'      => $book_num,
            'title'         => $single_title,
            'text'          => $text,
            'book'          => $book_title,
            'chapter_num'   => $chapter_num,
            'verses'        => $verse_display,
            'comments'      => $comments,
            'next_verse_link' => $next_verse_link,
            'prev_verse_link' => $prev_verse_link,
            'seo_title'     => $seo_title,
            'seo_description' => $seo_description,
            'theme'         => $_COOKIE['apptheme'] ?? '',
        ];

        $template = 'bible.verse';

        return view($template, $data);
    }

    public function ajaxTextChapter(Request $request)
    {
        $translation = $request->input('translation', 'rbo');
        $book_num = $request->input('book_num', 1);
        $chapter_num = $request->input('chapter_num', 1);
        $marked_verse = $request->input('marked_verse', 0);

        $translation = $this->clearTranslationParameter($translation);
        $book_num = $this->clearIntParameter($book_num);
        $chapter_num = $this->clearIntParameter($chapter_num);

        $args = [
            'book_num'      => $book_num,
            'chapter_num'   => $chapter_num
        ];

        $text = DB::table($translation)
            ->where($args)
            ->get();

        if ($text->count() === 0) {
            $args = [
                'book_num'      => $book_num,
                'chapter_num'   => 1
            ];

            $text = DB::table($translation)
                ->where($args)
                ->get();
        }

        if ($text->count() === 0) return abort(404);

        $book_title = $text[0]->book_name;

        $data = [
            'translation' => $translation,
            'book_num' => $book_num,
            'book' => $book_title,
            'chapter_num' => $chapter_num,
            'text' => $text,
            'marked_verse' => $marked_verse
        ];

        $template = 'partials.text-chapter';

        return view($template, $data);
    }

    function randomVerse(Request $request)
    {
        $translation = $request->input('translation', 'rbo');
        $max_id = DB::table($translation)->max('id');

        $random_id = rand(1, $max_id);

        $args = [
            'id'   => $random_id
        ];

        $text = DB::table($translation)
            ->where($args)
            ->get();

        $book_title = $text[0]->book_name;
        $single_title = $this->getWordsForTitle($text[0]->verse);
        $book_num = $text[0]->book_num;
        $chapter_num = $text[0]->chapter_num;
        $verse_display = $text[0]->verse_num;

        $seo_title = "Случайный отрывок из Библии" . ' | ' . env('APP_FULLNAME');
        $seo_description = "Генератор случайных отрывков из Библии. Бесполезная, но забавная функция. " . strtoupper($translation) . '. ' . env('APP_DESC');

        $translations = DB::table('bibles')->get();

        if ($translations->count() > 0) {
            $bibles = [];
            foreach ($translations as $item) {
                $bibles[$item->translation_slug] = $item->translation_name;
            }
        } else {
            $bibles = [];
        }

        $data = [
            'translation' => $translation,
            'book_num'  => $book_num,
            'title'     => $single_title,
            'text'      => $text,
            'book'      => $book_title,
            'chapter_num'   => $chapter_num,
            'verses'    => $verse_display,
            'bibles' => $bibles,
            'seo_title' => $seo_title,
            'seo_description' => $seo_description,
            'theme' => $_COOKIE['apptheme'] ?? '',
        ];

        return view('bible.random', $data);
    }

    function home(Request $request)
    {

        $ai = new AIController();
        $comments = $ai->getNewestComments(4);

        $seo_title = env('APP_FULLNAME');
        $seo_description = env('APP_DESC');
        $data = [
            'user_id'           => auth()->id() ?? 0,
            'comments'          => $comments,
            'book_num'          => $request->cookie('book_num'),
            'chapter_num'       => $request->cookie('chapter_num'),
            'seo_title'         => $seo_title,
            'seo_description'   => $seo_description,
            'theme'             => $_COOKIE['apptheme'] ?? '',
        ];

        $template = 'pages.home';

        return view($template, $data);
    }

    function getRandomVerse(Request $request)
    {
        $translation = $this->translations[array_rand($this->translations)];


        $max_id = DB::table($translation)->max('id');

        $random_id = rand(1, $max_id);

        $args = [
            'id'   => $random_id
        ];

        $text = DB::table($translation)
            ->where($args)
            ->get();

        $data = [
            'translation' => $translation,
            'book_title' => $text[0]->book_name,
            'book_num' => $text[0]->book_num,
            'chapter_num' => $text[0]->chapter_num,
            'verse_num' => $text[0]->verse_num,
            'verse_display' => $text[0]->verse,
        ];

        $template = 'partials.random-htmx';

        return view($template, $data);
    }
}
