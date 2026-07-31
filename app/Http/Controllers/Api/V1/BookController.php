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
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    /**
     * 書籍一覧を取得する
     *
     * @param SearchBookRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(SearchBookRequest $request): AnonymousResourceCollection
    {
        $filters = $request->only(['keyword', 'genre']);
        $perPage = $request->input('per_page', 20);

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
     *
     * @param Book $book
     * @return BookResource
     */
    public function show(Book $book): BookResource
    {
        $book->load('genres', 'reviews', 'reviews.user')
            ->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        return new BookResource($book);
    }

    /**
     * 書籍を更新する
     *
     * @param UpdatedBookRequest $request
     * @param Book $book
     * @return JsonResponse
     */
    public function update(UpdatedBookRequest $request, Book $book): JsonResponse
    {
        DB::transaction(function () use ($request, $book) {
            $book->update($request->validated());

            if ($request->has('genres')) {
                $book->genres()->sync($request->genres);
            }
        });

        return new BookResource($book->load('genres'))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * 書籍を削除する
     *
     * @param Book $book
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Book $book): JsonResponse
    {
        $this->authorize('delete', $book);

        $book->delete();

        return response()->json(null, 204);
    }
}
