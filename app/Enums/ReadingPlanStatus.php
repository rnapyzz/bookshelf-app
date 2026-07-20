<?php

namespace App\Enums;

enum ReadingPlanStatus: string
{
    case NotStarted = 'not_started';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::NotStarted => '未着手',
            self::Completed => '完了',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::NotStarted => 'bg-gray-100 text-gray-800',
            self::Completed => 'bg-green-100 text-green-800',
        };
    }
}
