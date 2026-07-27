<?php

namespace Tests\Feature;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function 認証ユーザーは読書計画一覧を表示できる(): void
    {
        $plan = ReadingPlan::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('reading-plans.index'));

        $response->assertStatus(200);
        $response->assertViewIs('reading-plans.index');
        $response->assertSee($plan->book->title);
    }

    /** @test */
    public function ステータスを指定して読書計画を絞り込み表示できる(): void
    {
        $planNotStarted = ReadingPlan::factory()->create([
            'user_id' => $this->user->id,
            'status' => ReadingPlanStatus::NotStarted,
        ]);
        $planCompleted = ReadingPlan::factory()->create([
            'user_id' => $this->user->id,
            'status' => ReadingPlanStatus::Completed,
        ]);

        $response = $this->actingAs($this->user)->get(route('reading-plans.index', ['status' => 'completed']));

        $response->assertStatus(200);
        $response->assertSee($planCompleted->book->title);
        $response->assertDontSee($planNotStarted->book->title);
    }

    /** @test */
    public function 認証ユーザーは読書計画の新規作成画面を表示できる(): void
    {
        $response = $this->actingAs($this->user)->get(route('reading-plans.create'));

        $response->assertStatus(200);
        $response->assertViewIs('reading-plans.create');
        $response->assertViewHas('books');
    }

    /** @test */
    public function 認証ユーザーは読書計画を新規作成できる(): void
    {
        $book = Book::factory()->create();

        $planData = [
            'book_id' => $book->id,
            'target_date' => now()->addDays(5)->toDateString(),
        ];

        $response = $this->actingAs($this->user)->post(route('reading-plans.store'), $planData);

        $response->assertRedirect(route('reading-plans.index'));
        $this->assertDatabaseHas('reading_plans', [
            'user_id' => $this->user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::NotStarted->value,
        ]);
    }

    /** @test */
    public function 自分の読書計画の編集画面を表示できる(): void
    {
        $plan = ReadingPlan::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('reading-plans.edit', $plan));

        $response->assertStatus(200);
        $response->assertViewIs('reading-plans.edit');
    }

    /** @test */
    public function 自分の読書計画を更新できる(): void
    {
        $plan =ReadingPlan::factory()->create([
            'user_id' => $this->user->id,
            'status' => ReadingPlanStatus::Expired,
        ]);

        $updateData = [
            'target_date' => now()->addDays(10)->toDateString(),
        ];

        $response = $this->actingAs($this->user)->put(route('reading-plans.update', $plan), $updateData);

        $response->assertRedirect(route('reading-plans.index'));
        $this->assertDatabaseHas('reading_plans' ,[
            'id' => $plan->id,
            'status' => ReadingPlanStatus::NotStarted->value,
        ]);
    }

    /** @test */
    public function 自分の読書計画を完了できる(): void
    {
        $plan = ReadingPlan::factory()->create([
            'user_id' => $this->user->id,
            'status' => ReadingPlanStatus::NotStarted,
        ]);

        $response = $this->actingAs($this->user)->post(route('reading-plans.complete', $plan));

        $response->assertRedirect(route('reading-plans.index'));
        $this->assertDatabaseHas('reading_plans', [
            'id' => $plan->id,
            'status' => ReadingPlanStatus::Completed,
        ]);
        $this->assertNotNull($plan->fresh()->completed_at);
    }

    /** @test */
    public function 自分の読書計画を削除できる(): void
    {
        $plan = ReadingPlan::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete(route('reading-plans.destroy', $plan));

        $response->assertRedirect(route('reading-plans.index'));
        $this->assertDatabaseMissing('reading_plans', [
            'id' => $plan->id,
        ]);
    }
}
