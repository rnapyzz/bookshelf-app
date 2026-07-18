<?php

use App\Http\Controllers\BookController;
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
});

Route::get('books', [BookController::class, 'index'])->name('books.index');
Route::get('books/{book}', [BookController::class, 'show'])->name('books.show');


// 動作確認用スタブ
Route::get('ranking', function () { return 'TODO'; })->name('ranking.index');
Route::get('favorites', function () { return 'TODO'; })->name('favorites.index');
Route::get('genres', function () { return 'TODO'; })->name('genres.index');
Route::post('reviews/{review}/like', function () { return 'TODO'; })->name('reviews.like');
Route::post('reviews/{book}', function () { return 'TODO'; })->name('reviews.store');
Route::post('favorites/{book}', function () { return 'TODO'; })->name('favorites.toggle');
