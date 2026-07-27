<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class BookResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しいResource形式に変換される(): void
    {
        $book = Book::factory()->create([
            'title' => 'Test Book',
            'author' => 'Mr. Testing',
            'isbn' => '9784000000000',
            'published_date' => '2026-07-01 00:00:00',
            'description' => 'test description',
            'image_url' => 'https://example.com/image.jpg',
        ]);

        $request = Request::create('/api/v1/books/'.$book->id, 'GET');
        $resource = new BookResource($book)->toArray($request);

        $this->assertEquals($book->id, $resource['id']);
        $this->assertEquals('Test Book', $resource['title']);
        $this->assertEquals('9784000000000', $resource['isbn']);
        $this->assertEquals('2026-07-01 00:00:00', (string) $resource['published_date']);
        $this->assertEquals('test description', $resource['description']);
        $this->assertEquals('https://example.com/image.jpg', $resource['image_url']);
        $this->assertEquals($book->created_at, $resource['created_at']);
        $this->assertEquals($book->updated_at, $resource['updated_at']);
    }
}
