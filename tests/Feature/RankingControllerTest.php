<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RankingControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ゲストユーザーでもランキング画面を表示できる(): void
    {
        $response = $this->get(route('ranking.index'));

        $response->assertStatus(200);
        $response->assertViewIs('ranking.index');
    }

    /** @test */
    public function レビューが存在する書籍のみがランキングに表示される(): void
    {
        Book::factory()->create(['title' => 'レビューがない書籍']);
        $bookWithReview = Book::factory()->create(['title' => 'レビューがある書籍']);
        Review::factory()->create([
            'book_id' => $bookWithReview->id,
            'rating' => 5,
        ]);

        $response = $this->get(route('ranking.index'));

        $response->assertStatus(200);
        $response->assertSee('レビューがある書籍');
        $response->assertDontSee('レビューがない書籍');
    }
}
