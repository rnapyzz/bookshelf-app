<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SearchBookRequest;
use App\Http\Requests\Api\V1\StoreBookRequest;
use App\Http\Requests\Api\V1\UpdatedBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
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
            ->loadAvg('reviews','rating')
            ->loadCount('reviews');

        return new BookResource($book);
    }

    /**
     * 書籍を登録する
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $book = Book::create($validated);

        if ($request->has('genres')) {
            $book->genres()->sync($request->genres);
        }

        return (new BookResource($book->load('genres')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * 書籍を更新する
     *
     * @throws AuthorizationException
     */
    public function update(UpdatedBookRequest $request, Book $book): JsonResponse
    {
        $this->authorize('update', $book);

        $book->update($request->validated());

        if ($request->has('genres')) {
            $book->genres()->sync($request->genres);
        }

        return (new BookResource($book->load('genres')))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * 書籍を削除する
     *
     * @throws AuthorizationException
     */
    public function destroy(Book $book): JsonResponse
    {
        $this->authorize('delete', $book);

        $book->delete();

        return response()->json(null, 204);
    }
}
