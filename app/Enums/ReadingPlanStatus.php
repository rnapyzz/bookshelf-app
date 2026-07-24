<?php

namespace App\Enums;

enum ReadingPlanStatus: string
{
    case NotStarted = 'not_started';
    case Expired = 'expired';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::NotStarted => '未読',
            self::Expired => '期限切れ',
            self::Completed => '読了',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::NotStarted => 'bg-gray-100 text-gray-800',
            self::Expired => 'bg-red-100 text-gray-800',
            self::Completed => 'bg-green-100 text-green-800',
        };
    }
}
