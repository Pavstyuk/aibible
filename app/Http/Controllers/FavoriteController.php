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
        if (!empty($request->user()->id)) {
            $user_id = $request->user()->id;
        } else {
            $user_id = 0;
        }

        if ($user_id === 0) {
            echo "<i class='bx bx-heart-break'></i>";
            exit;
        }

        $id_start = (int) $request->input('id_start');
        $id_end = (int) $request->input('id_end');
        $translation = $request->input('translation');

        if ($id_start === $id_end) {
            $ids = (string) $id_start;
        } else {
            $ids = "$id_start-$id_end";
        }

        // Получаем или создаем запись избранного
        $user_favorites = Favorite::firstOrCreate(
            ['user_id' => $user_id],
            ['user_id' => $user_id, 'verse_ids' => []] // инициализируем пустым массивом
        );

        // Получаем текущие избранное (теперь это будет массив)
        $favorites = $user_favorites->verse_ids ?? [];

        // Если для данного перевода еще нет массива, создаем его
        if (!isset($favorites[$translation])) {
            $favorites[$translation] = [];
        }

        // Добавляем id только если его еще нет (чтобы избежать дублей)
        if (!in_array($ids, $favorites[$translation])) {
            $favorites[$translation][] = $ids;
            $html_response = "<i class='bx bxs-heart'></i>";
        } else {
            // Удаляем id, если такой есть в массиве
            $i = array_search($ids, $favorites[$translation]);
            unset($favorites[$translation][$i]);
            $html_response = "<i class='bx bx-heart'></i>";
        }

        // Сохраняем
        $user_favorites->update([
            'verse_ids' => $favorites
        ]);

        echo $html_response;
    }

    static public function isFavorite(int $user_id = 0, string $translation = '', string $ids = '')
    {

        if ($user_id === 0 || $translation === '' || $ids === '') {
            return false;
        }

        $user_favorites = Favorite::where('user_id', $user_id)->first();

        if (!$user_favorites) {
            return false;
        }

        $favorites = $user_favorites->verse_ids;

        if (!isset($favorites[$translation])) {
            return false;
        }

        if (in_array($ids, $favorites[$translation])) {
            return true;
        }

        return false;
    }

    public function favoritesUser(Request $request, int $id)
    {
        if (!empty($request->user()->id)) {
            $user_id = (int) $request->user()->id;
        } else {
            $user_id = 0;
            return redirect()->route('login');
        }

        if ($user_id !== $id) {
            return abort(404);
        }

        $user_favorites = Favorite::where('user_id', $user_id)->first();

        $favorite_verses = [];

        if ($user_favorites) {
            $user_favorites_verse = (array) $user_favorites->verse_ids;
            foreach ($user_favorites_verse as $trans => $ids) {
                $ids_single = array_filter($ids, fn($id) => !strpos($id, '-'));
                $ids_group = array_filter($ids, fn($id) => strpos($id, '-'));

                $verses = DB::table($trans)->whereIn('id', $ids_single)->get()->all();

                $favorite_verses[$trans] = $verses;

                foreach ($ids_group as $group) {
                    $group_arr = explode('-', $group);
                    $favorite_verses[$trans][] = DB::table($trans)->whereBetween('id', [$group_arr[0],  $group_arr[1]])->get()->all();
                }
            }
        }

        // dump($favorite_verses);

        $seo_title = "Избранные отрывки из Библии Пользователя";
        $seo_description = "Избранные отрывки из Библии Пользователя";

        $data = [
            'favorites' => $favorite_verses,
            'user_id'       => $user_id,
            'book_num'          => $request->cookie('book_num'),
            'chapter_num'      => $request->cookie('chapter_num'),
            'seo_title'     => $seo_title,
            'seo_description' => $seo_description,
            'theme'         => $_COOKIE['apptheme'] ?? '',
        ];

        $template = 'bible.favorites';

        return view($template, $data);
    }
}
