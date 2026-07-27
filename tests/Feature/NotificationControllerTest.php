<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function 認証ユーザーは通知一覧画面を表示できる():void
    {
        $this->user->notifications()->create([
            'id' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
            'type' => 'App\Notifications\SampleNotification',
            'data' => ['message' => 'Test Notification'],
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->user)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertViewIs('notifications.index');
        $response->assertViewHas('notifications');
    }

    /** @test */
    public function 認証ユーザーは自分の未読の通知を既読にできる(): void
    {
        $notification = $this->user->notifications()->create([
            'id' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
            'type'=> 'App\Notifications\SampleNotification',
            'data' => ['message' => 'Test Notification'],
            'read_at' => null,
        ]);

        $this->assertNull($notification->fresh()->read_at);

        $response = $this->actingAs($this->user)->post(route('notifications.read', $notification->id));

        $response->assertRedirect(route('notifications.index'));
        $response->assertSessionHas('success', '通知を既読にしました');

        $this->assertNotNull($notification->fresh()->read_at);
    }
}
