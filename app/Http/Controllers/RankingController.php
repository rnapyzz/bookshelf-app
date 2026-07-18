<?php

namespace App\Http\Controllers;

use App\Models\Book;

class RankingController extends Controller
{
    public function index()
    {
        $rankedBooks = Book::query()
            ->has('reviews')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->limit(10)
            ->get();

        return view('ranking.index', compact('rankedBooks'));
    }
}
