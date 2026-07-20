<?php

namespace App\Http\Controllers;

use App\Http\Requests\Book\SearchBookRequest;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * 書籍一覧を表示する
     */
    public function index(SearchBookRequest $request): View
    {
        $filters = $request->only(['keyword', 'genre', 'sort']);

        $books = Book::query()
            ->with('genres')
            ->withAvg('reviews', 'rating')
            ->filter($filters)
            ->paginate(10)
            ->withQueryString();
        $genres = Genre::all();

        return view('books.index', compact('books', 'genres'));
    }

    /**
     * 書籍詳細画面を表示する
     */
    public function show(Book $book): View
    {
        $book->load([
            'genres', 'reviews.user', 'reviews.likedByUsers',
        ]);

        $likedReviewIds = Auth::check() ? Auth::user()->likedReviews()->pluck('review_id')->toArray() : [];

        return view('books.show', compact('book', 'likedReviewIds'));
    }

    /**
     * 書籍新規登録画面を表示する
     */
    public function create(): View
    {
        $genres = Genre::all();

        return view('books.create', compact('genres'));
    }

    /**
     * 書籍を新規登録する
     */
    public function store(StoreBookRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $book = Auth::user()->books()->create([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'isbn' => $validated['isbn'],
            'published_date' => $validated['published_date'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
        ]);

        if (! empty($validated['genres'])) {
            $book->genres()->sync($validated['genres']);
        }

        return redirect()->route('books.show', $book->id)->with('success', '書籍を登録しました');
    }

    /**
     * 書籍情報の編集画面を表示する
     *
     * @throws AuthorizationException
     */
    public function edit(Book $book): View
    {
        $this->authorize('update', $book);

        $genres = Genre::all();

        return view('books.edit', compact('book', 'genres'));
    }

    /**
     * 書籍情報を更新する
     *
     * @throws AuthorizationException
     */
    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $this->authorize('update', $book);

        $validated = $request->validated();

        $book->update([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'isbn' => $validated['isbn'],
            'published_date' => $validated['published_date'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
        ]);

        if (! empty($validated['genres'])) {
            $book->genres()->sync($validated['genres']);
        }

        return redirect()->route('books.show', $book)->with('success', '書籍情報を更新しました');
    }

    /**
     * 書籍情報を削除する
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        $book->genres()->detach();

        $book->delete();

        return redirect()->route('books.index')->with('success', '書籍を削除しました');
    }

    /**
     * ISBNからGoogle Books APIを利用して書籍情報を取得する
     */
    public function fetchByIsbn(string $isbn): JsonResponse
    {
        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => "isbn:{$isbn}",
        ]);

        if ($response->status() === 429) {
            return response()->json([
                'error' => 'APIの利用回数上限に達しました。しばらく時間をおいてから検索するか、書籍情報を手動で入力ください',
            ], 429);
        }

        if ($response->failed() || empty($response->json('items'))) {
            return response()->json(['error' => '書籍情報が見つかりませんでした'], 404);
        }

        $volumeInfo = $response->json('items.0.volumeInfo');

        return response()->json([
            'title' => $volumeInfo['title'] ?? null,
            'author' => isset($volumeInfo['authors']) ? implode(', ', $volumeInfo['authors']) : null,
            'description' => $volumeInfo['description'] ?? null,
            'image_url' => $volumeInfo['imageLinks']['thumbnail'] ?? null,
            'published_date' => $volumeInfo['publishedDate'] ?? null,
        ]);
    }
}
