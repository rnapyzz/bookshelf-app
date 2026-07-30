<?php

namespace Tests\Feature\Console;

use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

class KernelTest extends TestCase
{
    /** @test */
    public function books_send_reminderコマンドが毎日実行されるようにスケジュールされている(): void
    {
        $schedule = $this->app->make(Schedule::class);

        $events = $schedule->events();

        $reminderEvent = collect($events)->first(function ($event) {
            return str_contains($event->command, 'books:send-reminders');
        });

        $this->assertNotNull(
            $reminderEvent,
            'books:send-reminders コマンドがスケジュールに登録されていません'
        );

        $this->assertEquals('0 0 * * *', $reminderEvent->expression);
    }
}
