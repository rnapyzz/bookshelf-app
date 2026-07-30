<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 書籍一覧画面が正常に表示される(): void
    {
        Book::factory()->count(3)->create();

        $response = $this->get(route('books.index'));

        $response->assertStatus(200);
        $response->assertViewIs('books.index');
        $response->assertViewHas('books');
    }

    /** @test */
    public function 書籍詳細画面が正常に表示される(): void
    {
        $book = Book::factory()->create();

        $response = $this->get(route('books.show', $book));

        $response->assertStatus(200);
        $response->assertViewIs('books.show');
        $response->assertViewHas('book', $book);
    }

    /** @test */
    public function 存在しない書籍の詳細画面にアクセスした場合は404が返る(): void
    {
        $response = $this->get(route('books.show', 99999));

        $response->assertStatus(404);
    }

    /** @test */
    public function 認証済みユーザーは新規登録画面を表示できる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('books.create'));

        $response->assertStatus(200);
        $response->assertViewIs('books.create');
        $response->assertViewHas('genres');
    }

    /** @test */
    public function 未認証ユーザーは新規登録画面にアクセスできるログイン画面にリダイレクトされる(): void
    {
        $response = $this->get(route('books.create'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function 認証済みユーザーが正しいデータで書籍を新規登録できる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $bookData = [
            'title' => 'test title',
            'author' => 'tester',
            'isbn' => '9784000000001',
            'published_date' => '2026-07-24',
            'description' => 'test description',
            'image_url' => 'https://example.com/image100.jpg',
            'genres' => [$genre->id],
        ];

        $response = $this->actingAs($user)->post(route('books.store'), $bookData);

        $latestBook = Book::latest()->first();

        $response->assertRedirect(route('books.show', $latestBook));
        $this->assertDatabaseHas('books', [
            'title' => 'test title',
            'author' => 'tester',
            'isbn' => '9784000000001',
        ]);
        $this->assertDatabaseHas('book_genre', [
            'book_id' => $latestBook->id,
            'genre_id' => $genre->id,
        ]);
    }

    /** @test */
    public function バリデーションエラー時は書籍が登録されない(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('books.store'), [
            'title' => '',
            'author' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'author']);
        $this->assertDatabaseCount('books', 0);
    }

    /** @test */
    public function 書籍の所有者は編集画面を表示できる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('books.edit', $book));

        $response->assertStatus(200);
        $response->assertViewIs('books.edit');
    }

    /** @test */
    public function 書籍の所有者以外のユーザーが編集画面にアクセスすると認可エラーになる(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->get(route('books.edit', $book));

        $response->assertStatus(403);
    }

    /** @test */
    public function 書籍の所有者は書籍情報を更新できる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);
        $genre = Genre::factory()->create();

        $updateData = [
            'title' => 'Updated Title',
            'author' => 'tester',
            'isbn' => '9784000000001',
            'genres' => [$genre->id],
        ];

        $response = $this->actingAs($user)->put(route('books.update', $book), $updateData);

        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Updated Title',
        ]);
    }

    /** @test */
    public function 書籍の所有者は書籍を削除できる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('books.destroy', $book));

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }

    /** @test */
    public function 書籍の所有者以外は書籍を削除できない(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner]);

        $response = $this->actingAs($otherUser)->delete(route('books.destroy', $book));

        $response->assertStatus(403);
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
        ]);
    }

    /** @test */
    public function isbnから_google_books_apiで書籍情報を取得できる(): void
    {
        $user = User::factory()->create();

        Http::fake([
            'www.googleapis.com/books/v1/volumes*' => Http::response([
                'items' => [
                    [
                        'volumeInfo' => [
                            'title' => 'プログラミングRust 第2版',
                            'authors' => [
                                'Jim Blandy',
                                'Jason Orendorff',
                                'Leonora F. S. Tindall',
                            ],
                            'description' => 'Rustは、安全性、高速性、並行性に優れた言語。概要と用途について書かれた書籍で、ほとんどの機能を詳細にカバーした決定版。',
                            'imageLinks' => [
                                'thumbnail' => 'http://example.com/thumbnail.jpg',
                            ],
                            'publishedDate' => '2022-01-19',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $isbn = '9784873119786';

        $response = $this->actingAs($user)->getJson("/books/isbn/{$isbn}");

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'プログラミングRust 第2版',
                'author' => 'Jim Blandy, Jason Orendorff, Leonora F. S. Tindall',
                'description' => 'Rustは、安全性、高速性、並行性に優れた言語。概要と用途について書かれた書籍で、ほとんどの機能を詳細にカバーした決定版。',
                'image_url' => 'http://example.com/thumbnail.jpg',
                'published_date' => '2022-01-19',
            ]);
    }

    /** @test */
    public function google_books_apiが429エラーを返した際に適切にエラーレスポンスが返る(): void
    {

        $user = User::factory()->create();

        Http::fake([
            'www.googleapis.com/books/v1/volumes*' => Http::response(null, 429),
        ]);

        $response = $this->actingAs($user)->getJson('/books/isbn/9784873119786');

        $response->assertStatus(429)
            ->assertJson([
                'error' => 'APIの利用回数上限に達しました。しばらく時間をおいてから検索するか、書籍情報を手動で入力ください',
            ]);
    }

    /** @test */
    public function google_books_apiで書籍が見つからなかった場合は404が返る(): void
    {
        $user = User::factory()->create();

        Http::fake([
            'www.googleapis.com/books/v1/volumes*' => Http::response([
                'items' => [],
            ], 200),
        ]);

        $response = $this->actingAs($user)->getJson('/books/isbn/0000000000000');

        $response->assertStatus(404)
            ->assertJson([
                'error' => '書籍情報が見つかりませんでした',
            ]);
    }
}
