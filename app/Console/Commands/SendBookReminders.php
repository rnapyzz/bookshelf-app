<?php

namespace App\Console\Commands;

use App\Models\ReadingPlan;
use App\Notifications\BookReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBookReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '読書計画の期日をチェックし、ステータスの変更とリマインダー通知を行う';

    public function handle(): void
    {
        $today = Carbon::today();

        ReadingPlan::where('target_date', '<', $today)
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'expired')
            ->update(['status' => 'expired']);

        $threeDaysBefore = (clone $today)->addDays(3);
        $threeDaysAfter = (clone $today)->subDays(3);

        $this->sendNotificationsForDate($threeDaysBefore, 'three_days_before');
        $this->sendNotificationsForDate($today, 'on_due_date');
        $this->sendNotificationsForDate($threeDaysAfter, 'three_days_after');

        $this->info('リマインダー通知のバッチ処理が完了しました');
    }

    private function sendNotificationsForDate(Carbon $targetDate, string $timing): void
    {
        $plans = ReadingPlan::whereDate('target_date', $targetDate)
            ->where('status', '!=', 'completed')
            ->with('user')
            ->get();

        foreach ($plans as $plan) {
            if ($plan->user) {
                $plan->user->notify(new BookReminderNotification($plan, $timing));
            }
        }
    }
}
