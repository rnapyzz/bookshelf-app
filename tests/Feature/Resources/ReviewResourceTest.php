<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ReviewResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しい_resource形式に変換される(): void
    {
        $user = User::factory()->create([
            'name' => 'Testman',
            'email' => 'test@example.com',
        ]);

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'Perfect!!',
        ]);

        $request = Request::create('/api/v1/books'.$review->book_id, 'GET');
        $resource = new ReviewResource($review)->toArray($request);

        $this->assertEquals($review->id, $resource['id']);
        $this->assertEquals(5, $resource['rating']);
        $this->assertEquals('Perfect!!', $resource['comment']);
        $this->assertEquals($review->created_at, $resource['created_at']);
        $this->assertEquals($review->updated_at, $resource['updated_at']);
    }
}
