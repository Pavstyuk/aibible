<?php

namespace App\Http\Controllers;

use App\Traits\SanitizeTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class AudioController extends Controller
{
    use SanitizeTrait;

    public function index(Request $request)
    {
        $translation = $request->cookie("translation", "rbo");

        if (!$this->isValidTranslation($translation)) {
            $translation = $request->cookie("translation", "rbo");
        }

        $book_num = $request->cookie("book_num", 1);
        $chapter_num = $request->cookie("chapter_num", 1);

        $book_num = $this->clearIntParameter($book_num);
        $chapter_num = $this->clearIntParameter($chapter_num);

        $audio_translation = $request->input('atrans', 'rbo');
        if (!$this->isValidTranslation($audio_translation)) {
            $audio_translation = $request->cookie("atrans", "rbo");
        } else {
            Cookie::queue('atrans', $audio_translation, 1470);
        }
        if ((int) $request->input('abooknum') > 0 && $request->input('abooknum') <= 66) {
            $audio_book_num = (int) $request->input('abooknum');
            Cookie::queue('abooknum', $audio_book_num, 1470);
        } else {
            $audio_book_num = $request->cookie("abooknum", 1);
        }

        $chapters_list = DB::table($audio_translation)
            ->where('book_num', $audio_book_num)
            ->distinct()
            ->pluck('chapter_num')
            ->toArray();

        $abook_name = DB::table($audio_translation)
            ->where('book_num', $audio_book_num)
            ->distinct()
            ->pluck('book_name')
            ->toArray()[0];

        $books_list = DB::table($audio_translation)
            ->distinct()
            ->pluck('book_name', 'book_num')
            ->toArray();

        $track_list = [];

        foreach ($chapters_list as $num) {
            $track_list[$num] = config('app.url') . "/storage/audio/{$audio_translation}/{$audio_book_num}/{$num}.mp3";
        }

        $audio_translations = ['rst', 'rbo'];

        $data = [
            'audio_translation' => $audio_translation,
            'audio_book_num' => $audio_book_num,
            'audio_translations' => $audio_translations,
            'books_list' => $books_list,
            'track_list' => $track_list,
            "translation" => $translation,
            "abook_name" => $abook_name,
            "book_num" => $book_num,
            "chapter_num" => $chapter_num,
            'seo_title' => '',
            'seo_description' => '',
            "theme" => $_COOKIE["apptheme"] ?? "",
        ];

        return view('bible.audio', $data);
    }

    public function load(Request $request)
    {
        $audio_translation = $request->input('atrans', 'rbo');
        if (!$this->isValidTranslation($audio_translation)) {
            $audio_translation = $request->cookie("atrans", "rbo");
        } else {
            Cookie::queue('atrans', $audio_translation, 1470);
        }
        if ((int) $request->input('abooknum') > 0 && $request->input('abooknum') <= 66) {
            $audio_book_num = (int) $request->input('abooknum');
            Cookie::queue('abooknum', $audio_book_num, 1470);
        } else {
            $audio_book_num = $request->cookie("abooknum", 1);
        }


        $chapters_list = DB::table($audio_translation)
            ->where('book_num', $audio_book_num)
            ->distinct()
            ->pluck('chapter_num')
            ->toArray();

        $abook_name = DB::table($audio_translation)
            ->where('book_num', $audio_book_num)
            ->distinct()
            ->pluck('book_name')
            ->toArray()[0];

        $track_list = [];

        foreach ($chapters_list as $num) {
            $track_list[$num] = config('app.url') . "/storage/audio/{$audio_translation}/{$audio_book_num}/{$num}.mp3";
        }



        $data = [
            'abook_name' => $abook_name,
            'track_list' => $track_list,
        ];

        return view('partials.track-list', $data);
    }
}
