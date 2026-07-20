<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * マイ読書レポートを表示する
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $userReviews = Review::where('user_id', $user->id);

        // 基本統計
        $totalReviews = (clone $userReviews)->count();
        $booksRead = (clone $userReviews)->distinct('book_id')->count('book_id');
        $averageRating = (clone $userReviews)->avg('rating') ?? 0.0;

        $summary = [
            'total_reviews' => $totalReviews,
            'books_read' => $booksRead,
            'average_rating' => $averageRating,
        ];

        // 評価分布
        $ratingDist = collect([1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]);
        $rawDist = (clone $userReviews)
            ->selectRaw('rating, count(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');
        $ratingDist = $ratingDist->merge($rawDist);

        // 評価TOP5
        $topRatedBooks = Book::query()
            ->join('reviews', 'books.id', '=', 'reviews.book_id')
            ->where('reviews.user_id', $user->id)
            ->where('reviews.rating', '>=', 4)
            ->select('books.id', 'books.title', 'books.author', 'reviews.rating')
            ->orderByDesc('reviews.rating')
            ->orderByDesc('reviews.created_at')
            ->limit(5)
            ->get()
            ->toArray();

        // 評価傾向
        $genreRatings = Genre::query()
            ->join('book_genre', 'genres.id', '=', 'book_genre.genre_id')
            ->join('books', 'book_genre.book_id', '=', 'books.id')
            ->join('reviews', 'books.id', '=', 'reviews.book_id')
            ->where('reviews.user_id', $user->id)
            ->select('genres.id', 'genres.name')
            ->selectRaw('AVG(reviews.rating) as average_rating')
            ->selectRaw('COUNT(reviews.id) as count')
            ->groupBy('genres.id', 'genres.name')
            ->orderByDesc('average_rating')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->toArray();

        $stats = [
            'summary' => $summary,
            'rating_distribution' => $ratingDist,
            'top_rated_books' => $topRatedBooks,
            'genre_ratings' => $genreRatings,
        ];

        return view('reports.index', compact('stats'));
    }
}
