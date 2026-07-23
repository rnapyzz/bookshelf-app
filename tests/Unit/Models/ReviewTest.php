<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function レビューに紐づくユーザーを正しく取得できる(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $review->user->id);
    }

    /** @test */
    public function レビューに紐づく書籍を正しく取得できる(): void
    {
        $book = Book::factory()->create();
        $review = Review::factory()->create(['book_id' => $book->id]);

        $this->assertEquals($book->id, $review->book->id);
    }

    /** @test */
    public function レビューに紐づくいいねの一覧を取得できる(): void
    {
        $review = Review::factory()->create();
        ReviewLike::factory()->count(2)->create(['review_id' => $review->id]);

        $this->assertCount(2, $review->likes);
    }

    /** @test */
    public function レビューにいいねしたユーザー一覧を正しく取得できる(): void
    {
        $review = Review::factory()->create();
        $user = User::factory()->create();

        $review->likedByUsers()->attach($user->id);

        $this->assertCount(1, $review->likedByUsers);
        $this->assertEquals($user->id, $review->likedByUsers->first()->id);
    }
}
