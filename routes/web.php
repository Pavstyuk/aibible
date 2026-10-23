<?php

use App\Http\Controllers\AIController;
use App\Http\Controllers\BibleController;
use App\Http\Controllers\BibleMenuController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GigaChatController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\YandexGptController;
use Illuminate\Support\Facades\Route;

Route::prefix('htmx')->group(function () {
    Route::get('/menu-books', [BibleMenuController::class, 'books'])->name('menu-books');
    Route::get('/menu-chapters', [BibleMenuController::class, 'chapters'])->name('menu-chapters');
    Route::get('/menu-translations', [BibleMenuController::class, 'translations'])->name('menu-translations');
    Route::get('/text-chapter', [BibleController::class, 'ajaxTextChapter'])->name('textChapter');
    Route::post('/send-reg-link', [RegisterController::class, 'sendRegisterLink'])
        ->middleware('throttle:registration')
        ->name('send-reg-link');
    Route::get('/random-verse', [BibleController::class, 'getRandomVerse'])
        ->middleware('throttle:public')
        ->name('random-verse');

    Route::middleware('auth')->group(function () {
        Route::post('/ask-ai', [AIController::class, 'askAi'])
            ->middleware('throttle:ai')
            ->name('ask-ai');
        Route::post('/ask-giga', [GigaChatController::class, 'ask'])
            ->middleware('throttle:ai')
            ->name('ask-giga');
        Route::post('/ask-yandex', [YandexGptController::class, 'ask'])
            ->middleware('throttle:ai')
            ->name('ask-yandex');
        Route::post('/ask-local', [AIController::class, 'askLMStudio'])
            ->middleware('throttle:ai')
            ->name('ask-local');
        Route::post('/save-ai', [AIController::class, 'saveComment'])
            ->middleware('throttle:comments')
            ->name('save-ai');
        Route::get('/comments-ai', [AIController::class, 'getCommentsByUserId'])
            ->middleware('throttle:comments')
            ->name('comments-ai');
        Route::delete('/delete-ai', [AIController::class, 'deleteCommentById'])
            ->middleware('throttle:comments')
            ->name('delete-ai');
        Route::post('/add-favorite', [FavoriteController::class, 'toggleFavorite'])
            ->middleware('throttle:favorites')
            ->name('add-favorite');
    });
});

Route::prefix('user')->middleware('auth')->group(function () {
    Route::get('/{id}', [UserController::class, 'index'])->name('dashboard');
    Route::get('/{id}/edit', [UserController::class, 'editUser'])->name('edit-user');
    Route::post('/{id}/edit', [UserController::class, 'updateUser'])->name('update-user');
    Route::get('/{id}/favorites', [FavoriteController::class, 'favoritesUser'])->name('favorites-user');
    Route::get('/{id}/ai-comments', [AIController::class, 'displayCommentsByUserId'])->name('ai-comments-user');
});

Route::get('/search', [SearchController::class, 'search'])
    ->middleware('throttle:public')
    ->name('search');
Route::get('/random', [BibleController::class, 'randomVerse'])
    ->middleware('throttle:public')
    ->name('random');

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'store'])
    ->middleware('throttle:login')
    ->name('login.store');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'index'])->name('registration');
Route::post('/register', [RegisterController::class, 'store'])
    ->middleware('throttle:registration')
    ->name('registration.store');

Route::get('/restore', [RegisterController::class, 'restore'])->name('restore');
Route::post('/restore', [RegisterController::class, 'initRestore'])
    ->middleware('throttle:registration')
    ->name('restore-init');

Route::get('/policy', [PageController::class, 'policy'])->name('policy');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');

Route::get('/', [BibleController::class, 'home'])->name('home');

Route::get('/{translation}/{book_num?}/{chapter?}/', [BibleController::class, 'index'])->name('chapter');

Route::get('/{translation}/{book_num}/{chapter}/{verse}', [BibleController::class, 'single'])->name('single');
