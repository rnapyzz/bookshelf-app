<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SearchBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    /**
     * 書籍一覧を取得する
     */
    public function index(SearchBookRequest $request): AnonymousResourceCollection
    {
        $filters = $request->only(['keyword', 'genre']);
        $perPage = $request->input('per_page', 10);

        $books = Book::query()
            ->with('genres')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->filter($filters)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return BookResource::collection($books);
    }

    /**
     * 書籍詳細を取得する
     */
    public function show(Book $book): BookResource
    {
        $book->load('genres', 'reviews', 'reviews.user')
            ->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        return new BookResource($book);
    }
}
