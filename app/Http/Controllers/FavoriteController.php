<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    /**
     * AJAX Method
     */
    public function toggleFavorite(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response("<i class='bx bx-heart-break'></i>", 401);
        }

        $idStartInput = $request->input('id_start');
        $idEndInput = $request->input('id_end');
        $translationInput = $request->input('translation');
        $idStart = is_scalar($idStartInput) ? (int) $idStartInput : 0;
        $idEnd = is_scalar($idEndInput) ? (int) $idEndInput : 0;
        $translation = strtolower((string) preg_replace(
            '/[^a-z]/',
            '',
            is_string($translationInput) ? $translationInput : ''
        ));

        if (! in_array($translation, config('bible.translations', []), true)) {
            return response('Неверный перевод', 422);
        }

        if ($idStart < 1 || $idEnd < $idStart) {
            return response('Неверный отрывок', 422);
        }

        $ids = $idStart === $idEnd ? (string) $idStart : "{$idStart}-{$idEnd}";

        $userFavorites = Favorite::firstOrCreate(
            ['user_id' => $user->id],
            ['user_id' => $user->id, 'verse_ids' => []]
        );

        $favorites = (array) ($userFavorites->verse_ids ?? []);

        if (! isset($favorites[$translation]) || ! is_array($favorites[$translation])) {
            $favorites[$translation] = [];
        }

        if (! in_array($ids, $favorites[$translation], true)) {
            $favorites[$translation][] = $ids;
            $htmlResponse = "<i class='bx bxs-heart'></i>";
        } else {
            $index = array_search($ids, $favorites[$translation], true);

            if ($index !== false) {
                unset($favorites[$translation][$index]);
            }

            $favorites[$translation] = array_values($favorites[$translation]);
            $htmlResponse = "<i class='bx bx-heart'></i>";
        }

        $userFavorites->update(['verse_ids' => $favorites]);

        return response($htmlResponse);
    }

    public static function isFavorite(int $user_id = 0, string $translation = '', string $ids = '')
    {
        if (
            $user_id === 0
            || $translation === ''
            || $ids === ''
            || ! in_array($translation, config('bible.translations', []), true)
            || ! preg_match('/^\d+(?:-\d+)?$/', $ids)
        ) {
            return false;
        }

        $userFavorites = Favorite::where('user_id', $user_id)->first();

        if (! $userFavorites) {
            return false;
        }

        $favorites = (array) ($userFavorites->verse_ids ?? []);
        $items = $favorites[$translation] ?? [];

        return is_array($items) && in_array($ids, $items, true);
    }

    public function favoritesUser(Request $request, int $id)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $user_id = (int) $user->id;

        if ($user_id !== $id) {
            return abort(404);
        }

        $userFavorites = Favorite::where('user_id', $user_id)->first();
        $favoriteVerses = [];

        if ($userFavorites) {
            foreach ((array) ($userFavorites->verse_ids ?? []) as $translation => $ids) {
                if (! in_array($translation, config('bible.translations', []), true)) {
                    continue;
                }

                $ids = array_values(array_filter(
                    (array) $ids,
                    fn ($id) => is_string($id) && preg_match('/^\d+(?:-\d+)?$/', $id)
                ));

                $idsSingle = array_filter($ids, fn ($id) => ! str_contains($id, '-'));
                $idsGroup = array_filter($ids, fn ($id) => str_contains($id, '-'));

                $verses = DB::table($translation)
                    ->whereIn('id', array_map('intval', $idsSingle))
                    ->get()
                    ->all();

                $favoriteVerses[$translation] = $verses;

                foreach ($idsGroup as $group) {
                    [$start, $end] = array_map('intval', explode('-', $group, 2));

                    if ($start > 0 && $end >= $start) {
                        $favoriteVerses[$translation][] = DB::table($translation)
                            ->whereBetween('id', [$start, $end])
                            ->get()
                            ->all();
                    }
                }
            }
        }

        $seo_title = 'Избранные отрывки из Библии Пользователя';
        $seo_description = 'Избранные отрывки из Библии Пользователя';

        $data = [
            'favorites' => $favoriteVerses,
            'user_id' => $user_id,
            'book_num' => $request->cookie('book_num'),
            'chapter_num' => $request->cookie('chapter_num'),
            'seo_title' => $seo_title,
            'seo_description' => $seo_description,
            'theme' => $_COOKIE['apptheme'] ?? '',
        ];

        return view('bible.favorites', $data);
    }
}
