<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookReminderNotification extends Notification
{
    use Queueable;

    public function __construct($plan, string $timing)
    {
        $this->plan = $plan;
        $this->timing = $timing;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $title = $this->plan->book->title ?? '書籍';

        $message = match ($this->timing) {
            'three_days_before' => "「{$title}」の期日が3日後です",
            'on_due_date' => "「{$title}」の期日は本日です",
            'three_days_after' => "「{$title}」の期日が3日経過しています",
            default => "「{$title}」のリマインダーです",
        };

        return [
            'timing' => $this->timing,
            'title' => '書籍リマインダー',
            'body' => $message,
        ];
    }
}
