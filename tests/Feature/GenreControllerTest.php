<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    /**
     * テスト用のユーザーを作成する
     *
     * @return void
     */
    protected function setUp():void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function 認証ユーザーはジャンル一覧を表示できる(): void
    {
        $genre = Genre::factory()->create();

        $response = $this->actingAs($this->user)->get(route('genres.index'));

        $response->assertStatus(200);
        $response->assertViewIs('genres.index');
        $response->assertSee($genre->name);
    }

    /** @test */
    public function 認証ユーザーはジャンル詳細画面が表示できる(): void
    {
        $genre = Genre::factory()->create();

        $response = $this->actingAs($this->user)->get(route('genres.show', $genre));

        $response->assertStatus(200);
        $response->assertViewIs('genres.show');
        $response->assertSee($genre->name);
    }

    /** @test */
    public function 認証ユーザーはジャンル作成画面が表示できる(): void
    {
        $response = $this->actingAs($this->user)->get(route('genres.create'));

        $response->assertStatus(200);
        $response->assertViewIs('genres.create');
    }

    /** @test */
    public function 認証ユーザーは新しいジャンルを作成できる(): void
    {
        $genreData = [
            'name' => 'Test',
        ];

        $response = $this->actingAs($this->user)->post(route('genres.store'), $genreData);

        $response->assertRedirect(route('genres.index'));
        $this->assertDatabaseHas('genres', $genreData);
    }

    /** @test */
    public function 認証ユーザーはジャンル編集画面が表示できる(): void
    {
        $genre = Genre::factory()->create();

        $response = $this->actingAs($this->user)->get(route('genres.edit', $genre));

        $response->assertStatus(200);
        $response->assertViewIs('genres.edit');
        $response->assertSee($genre->name);
    }

    /** @test */
    public function 認証ユーザーはジャンルを更新できる(): void
    {
        $genre = Genre::factory()->create(['name' => 'Test']);

        $updateData = [
            'name' => 'Test(Updated)',
        ];

        $response = $this->actingAs($this->user)->put(route('genres.update', $genre), $updateData);

        $response->assertRedirect(route('genres.index'));
        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => 'Test(Updated)',
        ]);
    }

    /** @test */
    public function 認証ユーザーは書籍が紐づいていないジャンルを削除できる(): void
    {
        $genre = Genre::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('genres.destroy', $genre));

        $response->assertRedirect(route('genres.index'));
        $this->assertDatabaseMissing('genres', [
            'id' => $genre->id,
        ]);
    }

    /** @test */
    public function 書籍が紐づいているジャンルは削除できない(): void
    {
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();
        $genre->books()->attach($book);

        $response = $this->actingAs($this->user)->delete(route('genres.destroy', $genre));

        $response->assertRedirect();
        $response->assertSessionHas('error', '書籍が登録されているためジャンルを削除できません');
        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
        ]);
    }
}
