<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しい_resource形式に変換される(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $request = Request::create('/api/v1/books/1', 'GET');
        $resource = new UserResource($user)->toArray($request);

        $this->assertEquals([
            'name' => 'Test User',
        ], $resource);
    }
}
