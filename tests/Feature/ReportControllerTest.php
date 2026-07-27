<?php

namespace Tests\Feature;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function 認証ユーザーはマイ読書レポート画面を表示できる(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertViewIs('reports.index');
        $response->assertViewHas('stats');
    }

    /** @test */
    public function レポートの基本統計と評価分布が正しく計算される(): void
    {
        $book = Book::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $this->user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::Completed,
        ]);

        Review::factory()->create([
            'user_id' => $this->user->id,
            'book_id' => $book->id,
            'rating' => 4,
        ]);

        $book2 = Book::factory()->create();
        Review::factory()->create([
            'user_id' => $this->user->id,
            'book_id' => $book2->id,
            'rating' => 5,
        ]);

        $response = $this->actingAs($this->user)->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            $summary = $stats['summary'];
            $this->assertEquals(2, $summary['total_reviews']);
            $this->assertEquals(1, $summary['books_read']);
            $this->assertEquals(4.5, $summary['average_rating']);

            $ratingDist = $stats['rating_distribution'];
            $this->assertEquals(1, $ratingDist[3]);
            $this->assertEquals(1, $ratingDist[4]);

            return true;
        });
    }
}
