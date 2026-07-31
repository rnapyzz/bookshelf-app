<?php

namespace Tests\Feature\Exceptions;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HandlerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function apiでモデルが見つからない場合は404メッセージを返す(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/books/9999999');

        $response->assertStatus(404)
            ->assertJson([
                'message' => '書籍が見つかりません',
            ]);
    }

    /** @test */
    public function apiで存在しないエンドポイントにアクセスした場合は404メッセージを返す(): void
    {
        $response = $this->getJson('/api/v1/non-existent-endpoint');

        $response->assertStatus(404)
            ->assertJson([
                'message' => '指定されたエンドポイントが見つかりません',
            ]);
    }

    /** @test */
    public function apiで権限がない操作を行った場合は403メッセージを返す(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $book = Book::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->putJson("/api/v1/books/{$book->id}", [
            'title' => 'Update Test',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'この操作を行う権限がありません',
            ]);
    }

    /** @test */
    public function apiで見認証の場合は401メッセージを返す(): void
    {
        $response = $this->postJson('/api/v1/books', []);

        $response->assertStatus(401)
            ->assertJson([
                'message' => '認証トークンが存在しないか、無効です。ログインしてください',
            ]);
    }

    /** @test */
    public function apiで許可されていない_htt_pメソッドの場合は405メッセージを返す(): void
    {
        $book = Book::factory()->create();

        $response = $this->postJson("/api/v1/books/{$book->id}", []);

        $response->assertStatus(405)
            ->assertJson([
                'message' => '許可されていないHTTPメソッドです',
            ]);
    }
}
