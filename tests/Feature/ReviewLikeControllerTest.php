<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewLikeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function 認証ユーザーはレビューにいいねできる(): void
    {
        $review = Review::factory()->create();

        $response = $this->actingAs($this->user)->post(route('reviews.like', $review));

        $response->assertRedirect();
        $this->assertDatabaseHas('review_likes', [
            'user_id' => $this->user->id,
            'review_id' => $review->id,
        ]);
    }

    /** @test */
    public function いいねを取り消すことができる(): void
    {
        $review = Review::factory()->create();

        $this->user->likedReviews()->attach($review->id);

        $response = $this->actingAs($this->user)->post(route('reviews.like', $review));

        $response->assertRedirect();
        $this->assertDatabaseMissing('review_likes', [
            'user_id' => $this->user->id,
            'review_id' => $review->id,
        ]);
    }
}
