<?php

namespace Tests\Feature\Console;

use App\Enums\ReadingPlanStatus;
use App\Models\ReadingPlan;
use App\Models\User;
use App\Notifications\BookReminderNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendBookRemindersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-07-30 10:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /** @test */
    public function 期限切れの未完了プランがexpiredステータスに更新される(): void
    {
        $user = User::factory()->create();

        $expiredPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'target_date' => '2026-07-25',
            'status' => 'not_started',
        ]);

        $completedPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'target_date' => '2026-07-25',
            'status' => 'completed',
        ]);

        $this->artisan('books:send-reminders')
            ->assertExitCode(0);

        $this->assertEquals(ReadingPlanStatus::Expired, $expiredPlan->fresh()->status);
        $this->assertEquals(ReadingPlanStatus::Completed, $completedPlan->fresh()->status);
    }

    /** @test */
    public function 当日_3日前_3日後のユーザーにそれぞれ正しいリマインダー通知が送信される(): void
    {
        Notification::fake();

        $user3DaysBefore = User::factory()->create();
        $userOnDueDate = User::factory()->create();
        $user3DaysAfter = User::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user3DaysBefore->id,
            'target_date' => '2026-08-02',
            'status' => 'not_started',
        ]);

        ReadingPlan::factory()->create([
            'user_id' => $userOnDueDate->id,
            'target_date' => '2026-07-30',
            'status' => 'not_started',
        ]);

        ReadingPlan::factory()->create([
            'user_id' => $user3DaysAfter->id,
            'target_date' => '2026-07-27',
            'status' => 'not_started',
        ]);

        $this->artisan('books:send-reminders')
            ->expectsOutput('リマインダー通知のバッチ処理が完了しました')
            ->assertExitCode(0);

        Notification::assertSentTo(
            [$user3DaysBefore],
            function (BookReminderNotification $notification)
            {
                $data = $notification->toArray(new User());
                return $data['timing'] === 'three_days_before';
            }
        );

        Notification::assertSentTo(
            [$userOnDueDate],
            function (BookReminderNotification $notification)
            {
                $data = $notification->toArray(new User());
                return $data['timing'] === 'on_due_date';
            }
        );

        Notification::assertSentTo(
            [$user3DaysAfter],
            function (BookReminderNotification $notification)
            {
                $data = $notification->toArray(new User());
                return $data['timing'] === 'three_days_after';
            }
        );
   }
}
