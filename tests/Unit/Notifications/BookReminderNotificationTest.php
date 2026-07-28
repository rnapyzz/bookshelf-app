<?php

namespace Tests\Unit\Notifications;

use App\Models\User;
use App\Notifications\BookReminderNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Mockery;
use Tests\TestCase;

class BookReminderNotificationTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();;
        parent::tearDown();
    }

    /** @test */
    public function 配信チャンネルとしてdatabaseを返す(): void
    {
        $notification = new BookReminderNotification(null, 'on_due_date');
        $user = new User();

        $channels = $notification->via($user);

        $this->assertEquals(['database'], $channels);
    }

    /** @test */
    public function ３日前用の通知データの配列が正しく生成される(): void
    {
        $book = (object) ['title' => 'Test Book 3d before'];
        $plan = (object) ['book' => $book];

        $notification = new BookReminderNotification($plan, 'three_days_before');
        $user = new User();

        $data = $notification->toArray($user);

        $this->assertEquals([
            'timing' => 'three_days_before',
            'title' => '書籍リマインダー',
            'body' => '「Test Book 3d before」の期日が3日後です',
        ], $data);
    }

    /** @test */
    public function 期日当日の通知データの配列が正しく生成される(): void
    {
        $book = (object) ['title' => 'Test Book today'];
        $plan = (object) ['book' => $book];

        $notification = new BookReminderNotification($plan, 'on_due_date');
        $user = new User();

        $data = $notification->toArray($user);

        $this->assertEquals([
            'timing' => 'on_due_date',
            'title' => '書籍リマインダー',
            'body' => '「Test Book today」の期日は本日です',
        ], $data);
    }

    /** @test */
    public function ３日後用の通知データの配列が正しく生成される(): void
    {
        $book = (object) ['title' => 'Test Book 3d after'];
        $plan = (object) ['book' => $book];

        $notification = new BookReminderNotification($plan, 'three_days_after');
        $user = new User();

        $data = $notification->toArray($user);

        $this->assertEquals([
            'timing' => 'three_days_after',
            'title' => '書籍リマインダー',
            'body' => '「Test Book 3d after」の期日が3日経過しています',
        ], $data);
    }

    /** @test */
    public function 未設定でのタイミングでの場合はデフォルトメッセージが生成される(): void
    {
        $book = (object) ['title' => 'Test Book today'];
        $plan = (object) ['book' => $book];

        $notification = new BookReminderNotification($plan, 'unknown');
        $user = new User();

        $data = $notification->toArray($user);

        $this->assertEquals([
            'timing' => 'unknown',
            'title' => '書籍リマインダー',
            'body' => '「Test Book today」のリマインダーです',
        ], $data);

    }


    /** @test */
    public function 書籍タイトルが取得できない場合はデフォルトの書籍名で生成される(): void
    {
        $plan = (object) ['book' => null];

        $notification = new BookReminderNotification($plan, 'on_due_date');
        $user = new User();

        $data = $notification->toArray($user);

        $this->assertEquals([
            'timing' => 'on_due_date',
            'title' => '書籍リマインダー',
            'body' => '「書籍」の期日は本日です',
        ], $data);
    }

    /** @test */
    public function メール通知の内容が正しく構築される(): void
    {
        $notification = new BookReminderNotification(null, 'on_due_date');
        $user = new User();

        $mailMessage = $notification->toMail($user);

        $arrayData = $mailMessage->toArray();

        $this->assertContains('The introduction to the notification.', $arrayData['introLines']);
        $this->assertEquals('Notification Action', $arrayData['actionText']);
        $this->assertEquals(url('/'), $arrayData['actionUrl']);
        $this->assertContains('Thank you for using our application!', $arrayData['outroLines']);
    }
}
