<?php

use App\Http\Controllers\AIController;
use App\Http\Controllers\BibleController;
use App\Http\Controllers\BibleMenuController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

Route::get('/redis-test', function () {
    try {
        Redis::set('test_key', 'Hello from Laravel + Redis!');
        $value = Redis::get('test_key');

        Cache::put('cache_test', 'Cached value', 600); // 10 минут
        $cached = Cache::get('cache_test');

        Redis::incr('page_views');
        $views = Redis::get('page_views');

        return [
            'redis_direct' => $value,
            'cache_driver' => $cached,
            'page_views' => $views,
            'status' => 'connected'
        ];
    } catch (\Exception $e) {
        return [
            'error' => $e->getMessage(),
            'status' => 'failed'
        ];
    }
});

Route::prefix('htmx')->group(function () {
    Route::get('/menu-books', [BibleMenuController::class, 'books'])->name('menu-books');
    Route::get('/menu-chapters', [BibleMenuController::class, 'chapters'])->name('menu-chapters');
    Route::get('/menu-translations', [BibleMenuController::class, 'translations'])->name('menu-translations');
    Route::get('/text-chapter', [BibleController::class, 'ajaxTextChapter'])->name('textChapter');
    Route::get('/ask-ai', [AIController::class, 'askMistral'])->name('ask-ai');
    Route::get('/ask-open-router', [AIController::class, 'askOpenRouter'])->name('ask-open-router');
    Route::get('/ask-local', [AIController::class, 'askLMStudio'])->name('ask-local');
    Route::post('/save-ai', [AIController::class, 'saveComment'])->name('save-ai');
    Route::get('/comments-ai', [AIController::class, 'getCommentsByUserId'])->name('comments-ai');
    Route::delete('/delete-ai', [AIController::class, 'deleteCommentById'])->name('delete-ai');
    Route::post('/send-reg-link', [RegisterController::class, 'sendRegisterLink'])->name('send-reg-link');
    Route::post('/add-favorite', [FavoriteController::class, 'toggleFavorite'])->name('add-favorite');
    Route::get('/random-verse', [BibleController::class, 'getRandomVerse'])->name('random-verse');
});
Route::prefix('user')->group(function () {
    Route::get('/{id}', [UserController::class, 'index'])->name('dashboard');
    Route::get('/{id}/edit', [UserController::class, 'editUser'])->name('edit-user');
    Route::post('/{id}/edit', [UserController::class, 'updateUser'])->name('update-user');
    Route::get('/{id}/favorites', [FavoriteController::class, 'favoritesUser'])->name('favorites-user');
    Route::get('/{id}/ai-comments', [AIController::class, 'displayCommentsByUserId'])->name('ai-comments-user');
});

Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/random', [BibleController::class, 'randomVerse'])->name('random');


Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'index'])->name('registration');
Route::post('/register', [RegisterController::class, 'store'])->name('registration.store');

Route::get('/restore', [RegisterController::class, 'restore'])->name('restore');
Route::post('/restore', [RegisterController::class, 'initRestore'])->name('restore-init');

// Route::get('/notes', [NoteController::class, 'index'])->name('notes');
// Route::get('/{user}/notes', [NoteController::class, 'userNotes'])->name('notes.user');
// Route::get('/{user}/notes/create', [NoteController::class, 'create'])->name('note.create');
// Route::get('/{user}/notes/{id}/edit', [NoteController::class, 'edit'])->name('note.edit');
// Route::get('/{user}/notes/{slug}', [NoteController::class, 'show'])->name('note.show');
// Route::post('/{user}/notes/store', [NoteController::class, 'store'])->name('note.store');
// Route::put('/{user}/notes/{id}', [NoteController::class, 'update'])->name('note.update');
// Route::delete('/{user}/notes/{id}', [NoteController::class, 'delete'])->name('note.delete');

// Route::get('/ai/{translation}/{verse_id}', [AIController::class, 'getCommentsByVerseID'])->name('verse-comments');

Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');

Route::get('/', [BibleController::class, 'home'])->name('home');

Route::get('/{translation}/{book_num?}/{chapter?}/', [BibleController::class, 'index'])->name('chapter');

Route::get('/{translation}/{book_num}/{chapter}/{verse}', [BibleController::class, 'single'])->name('single');
