<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewLikeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [BookController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('books', BookController::class)->except(['index', 'show']);
    Route::post('/books/{book}/favorites', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/books/isbn/{isbn}', [BookController::class, 'fetchByIsbn']);
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/reviews/{review}/like', [ReviewLikeController::class, 'toggle'])->name('reviews.like');
    Route::resource('genres', GenreController::class);
});

Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');


// スタブ
Route::get('/reports', function () { return 'TODO'; })->name('reports.index');
Route::get('/reading-plans', function () { return 'TODO'; })->name('reading-plans.index');
Route::get('/notifications', function () { return 'TODO'; })->name('notifications.index');
