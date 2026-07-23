<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ジャンルに紐づく書籍を正しく取得できる(): void
    {
        $genre = Genre::factory()->create();

        $books = Book::factory()->count(2)->create();
        $genre->books()->attach($books);

        $this->assertCount(2, $genre->books);
        $this->assertEquals(
            $books->pluck('id')->sort()->values(),
            $genre->books->pluck('id')->sort()->values(),
        );
    }
}
