<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function 認証ユーザーはレビューを投稿できる(): void
    {
        $book = Book::factory()->create();

        $reviewData = [
            'rating' => 5,
            'comment' => '最高です',
        ];

        $response = $this->actingAs($this->user)->post(route('reviews.store', $book), $reviewData);

        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseHas('reviews', [
            'book_id' => $book->id,
            'user_id' => $this->user->id,
            'rating' => 5,
            'comment' => '最高です',
        ]);
    }

    /** @test */
    public function 自分のレビューの編集画面を表示できる(): void
    {
        $review = Review::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('reviews.edit', $review));

        $response->assertStatus(200);
        $response->assertViewIs('reviews.edit');
        $response->assertSee($review->comment);
    }

    /** @test */
    public function 他人のレビューの編集画面にはアクセスできない(): void
    {
        $otherUser = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->get(route('reviews.edit', $review));

        $response->assertForbidden();
    }

    /** @test */
    public function 自分のレビューを更新できる(): void
    {
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'rating' => 3,
            'comment' => '普通です',
        ]);

        $updateData = [
            'rating' => 4,
            'comment' => '参考になりました',
        ];

        $response = $this->actingAs($this->user)->put(route('reviews.update', $review), $updateData);

        $response->assertRedirect(route('books.show', $review->book));
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 4,
            'comment' => '参考になりました',
        ]);
    }

    /** @test */
    public function 他人のレビューは更新できない(): void
    {
        $otherUser = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $otherUser->id]);

        $updateData = [
            'rating' => 5,
            'comment' => '更新',
        ];

        $response = $this->actingAs($this->user)->put(route('reviews.update', $review), $updateData);

        $response->assertForbidden();
    }

    /** @test */
    public function 自分のレビューを削除できる(): void
    {
        $review = Review::factory()->create(['user_id' => $this->user->id]);
        $bookId = $review->book_id;

        $response = $this->actingAs($this->user)->delete(route('reviews.destroy', $review));

        $response->assertRedirect(route('books.show', $bookId));
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    /** @test */
    public function 他人のレビューは削除できない(): void
    {
        $otherUser = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->delete(route('reviews.destroy', $review));

        $response->assertForbidden();
        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
    }
}
