<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * 書籍一覧を表示する
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
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

    public function show(Book $book)
    {
        $book->load([
            'genres', 'reviews.user', 'reviews.likedByUsers',
        ]);

        $likedReviewIds = Auth::check() ? Auth::user()->likedReviews()->pluck('review_id')->toArray() : [];

        return view('books.show', compact('book', 'likedReviewIds'));
    }

    public function create()
    {
        $genres = Genre::all();

        return view('books.create', compact('genres'));
    }

    public function store(StoreBookRequest $request)
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

    public function edit(Book $book)
    {
        $this->authorize('update', $book);

        $genres = Genre::all();

        return view('books.edit', compact('book', 'genres'));
    }

    public function update(UpdateBookRequest $request, Book $book)
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

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        $book->genres()->detach();

        $book->delete();

        return redirect()->route('books.index')->with('success', '書籍を削除しました');
    }
}
