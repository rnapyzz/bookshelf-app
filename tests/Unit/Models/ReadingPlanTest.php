<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 読書計画に紐づくユーザーを正しく取得できる(): void
    {
        $user = User::factory()->create();
        $readingPlan = ReadingPlan::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $readingPlan->user->id);
    }

    /** @test */
    public function 読書計画に紐づく書籍を正しく取得できる(): void
    {
        $book = Book::factory()->create();
        $readingPlan = ReadingPlan::factory()->create(['book_id' => $book->id]);

        $this->assertEquals($book->id, $readingPlan->book->id);
    }
}
