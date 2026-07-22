<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 書籍に紐づくジャンルを正しく取得できる(): void
    {
        $book = Book::factory()->create();
        $genres = Genre::factory()->count(2)->create();

        $book->genres()->attach($genres);

        $this->assertCount(2, $book->genres);
        $this->assertEquals(
            $genres->pluck('id')->sort()->values(),
            $book->genres->pluck('id')->sort()->values(),
        );
    }

    /** @test */
    public function 書籍に紐づくレビューを正しく取得できる(): void
    {
        $book = Book::factory()->create();
        Review::factory()->count(3)->create(['book_id' => $book->id]);

        $this->assertCount(3, $book->reviews);
    }

    /** @test */
    public function 書籍をお気に入り登録したユーザーを正しく取得できる(): void
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();

        $book->favoritedByUsers()->attach($user->id);

        $this->assertCount(1, $book->favoritedByUsers);
        $this->assertEquals($user->id, $book->favoritedByUsers->first()->id);
    }

    /** @test */
    public function キーワード検索でタイトルまたは著者に一致する書籍が絞り込まれる(): void
    {
        Book::factory()->create(['title' => 'Laravel', 'author' => 'Laravel Man']);
        Book::factory()->create(['title' => 'Rust', 'author' => 'Rust Woman']);
        Book::factory()->create(['title' => 'React.js', 'author' => 'React Man']);

        $results = Book::filter(['keyword' => 'Laravel'])->get();
        $this->assertCount(1, $results);
        $this->assertEquals('Laravel', $results->first()->title);

        $results = Book::filter(['keyword' => 'Woman'])->get();
        $this->assertCount(1, $results);
        $this->assertEquals('Rust', $results->first()->title);
    }

    /** @test */
    public function キーワードが空の場合はすべての書籍が取得される(): void
    {
        Book::factory()->count(3)->create();

        $results = Book::filter(['keyword' => ''])->get();
        $this->assertCount(3, $results);
    }

    /** @test */
    public function ジャンルIDで書籍が正しく絞り込まれる(): void
    {
        $genre1 = Genre::factory()->create();
        $genre2 = Genre::factory()->create();

        $book1 = Book::factory()->create();
        $book1->genres()->attach($genre1);

        $book2 = Book::factory()->create();
        $book2->genres()->attach($genre2);

        $results = Book::filter(['genre' => $genre1->id])->get();

        $this->assertCount(1, $results);
        $this->assertEquals($book1->id, $results->first()->id);
    }

    /** @test */
    public function ソート条件に従って正しく並び替えが行われる(): void
    {
        $oldBook = Book::factory()->create(['title' => 'a old book', 'created_at' => now()->subMinutes(10)]);
        $newBook = Book::factory()->create(['title' => 'b new book', 'created_at' => now()]);

        $results = Book::filter(['sort' => 'newest'])->get();
        $this->assertEquals($newBook->id, $results->first()->id);

        $results = Book::filter(['sort' => 'oldest'])->get();
        $this->assertEquals($oldBook->id, $results->first()->id);

        $results = Book::filter(['sort' => 'title'])->get();
        $this->assertEquals($oldBook->id, $results->first()->id);
    }

    /** @test */
    public function ソート条件に従ってレビューの平均評価の降順で並び替えが行われる(): void
    {
        $lowBook = Book::factory()->create(['title' => 'Low Rated Book']);
        $highBook = Book::factory()->create(['title' => 'High Rated Book']);

        Review::factory()->create(['book_id' => $lowBook->id, 'rating' => 1]);
        Review::factory()->create(['book_id' => $lowBook->id, 'rating' => 2]);

        Review::factory()->create(['book_id' => $highBook->id, 'rating' => 4]);
        Review::factory()->create(['book_id' => $highBook->id, 'rating' => 5]);

        $results = Book::withAvg('reviews', 'rating')
            ->filter(['sort' => 'rating'])
            ->get();

        $this->assertEquals($highBook->id, $results->first()->id);
        $this->assertEquals($lowBook->id, $results->last()->id);
    }
}
