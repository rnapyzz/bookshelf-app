<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ユーザーが作成した書籍を正しく取得できる(): void
    {
        $user = User::factory()->create();
        $books = Book::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->books);
        $this->assertEquals(
            $books->pluck('id')->sort()->values(),
            $user->books->pluck('id')->sort()->values(),
        );
    }

    /** @test */
    public function ユーザーのお気に入り書籍を正しく取得できる(): void
    {
        $user = User::factory()->create();
        $books = Book::factory()->count(2)->create();

        $user->favoriteBooks()->attach($books->pluck('id'));

        $this->assertCount(2, $user->favoriteBooks);
        $this->assertEquals(
            $books->pluck('id')->sort()->values(),
            $user->favoriteBooks->pluck('id')->sort()->values(),
        );
    }

    /** @test */
    public function ユーザーがいいねしたレビューを正しく取得できる(): void
    {
        $user = User::factory()->create();
        $reviews = Review::factory()->count(2)->create();

        $user->likedReviews()->attach($reviews->pluck('id'));

        $this->assertCount(2, $user->likedReviews);
        $this->assertEquals(
            $reviews->pluck('id')->sort()->values(),
            $user->likedReviews->pluck('id')->sort()->values(),
        );
    }

    /** @test */
    public function ユーザーが作成した読書計画を正しく取得できる(): void
    {
        $user = User::factory()->create();
        $readingPlans = ReadingPlan::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->readingPlans);
        $this->assertEquals(
            $readingPlans->pluck('id')->sort()->values(),
            $user->readingPlans->pluck('id')->sort()->values(),
        );
    }
}
