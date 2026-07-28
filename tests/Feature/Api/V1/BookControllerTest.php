<?php

namespace Tests\Feature\Api\V1;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function 書籍一覧をページネーション付きで取得できる(): void
    {
        Book::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'author',
                        'isbn',
                        'published_date',
                        'description',
                        'image_url',
                        'genres',
                        'reviews_avg_rating',
                        'reviews_count',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    /** @test */
    public function 書籍詳細を取得できる(): void
    {
        $book = Book::factory()->create();
        $genre = Genre::factory()->create();
        $book->genres()->attach($genre);

        $response = $this->getJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $book->id)
            ->assertJsonPath('data.title', $book->title);
    }

    /** @test */
    public function 認証済みユーザーは書籍を登録できる(): void
    {
        Sanctum::actingAs($this->user);

        $genre = Genre::factory()->create();
        $bookData = [
            'title' => 'API TEST',
            'author' => 'API author',
            'isbn' => '9874000000000',
            'published_date' => '2026-07-28',
            'description' => 'API TEST description',
            'genres' => [$genre->id],
        ];

        $response = $this->postJson('/api/v1/books', $bookData);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'API TEST');

        $this->assertDatabaseHas('books', [
            'title' => 'API TEST',
            'user_id' => $this->user->id,
        ]);
        $this->assertDatabaseHas('book_genre', [
            'genre_id' => $genre->id,
        ]);
    }

    /** @test */
    public function 未認証ユーザーは書籍を登録できない(): void
    {
        $response = $this->postJson('/api/v1/books', [
            'title' => '未認証書籍',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function 認証ユーザーは自分の登録した書籍を更新できる(): void
    {
        Sanctum::actingAs($this->user);

        $book = Book::factory()->create(['user_id' => $this->user->id]);
        $genre = Genre::factory()->create();

        $updateData = [
            'title' => 'Updated Title',
            'author' => $book->author,
            'isbn' => $book->isbn,
            'published_date' => $book->published_date->toDateString(),
            'description' => $book->description,
            'genres' => [$genre->id],
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Title');
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Updated Title',
        ]);
    }

    /** @test */
    public function 他人の登録した書籍は更新できない(): void
    {
        $otherUser = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $otherUser->id]);
        $genre = Genre::factory()->create();

        Sanctum::actingAs($this->user);

        $response = $this->putJson("/api/v1/books/{$book->id}", [
            'title' => 'Invalid Update',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function 自分の登録した書籍は削除できる(): void
    {
        Sanctum::actingAs($this->user);

        $book = Book::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(204);
        $this->assertModelMissing($book);
    }

    /** @test */
    public function 他人の登録した書籍は削除できない(): void
    {
        $otherUser = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $otherUser->id]);

        Sanctum::actingAs($this->user);

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(403);
        $this->assertModelExists($book);
    }
}
