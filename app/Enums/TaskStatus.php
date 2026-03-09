<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('app.status_pending'),
            self::InProgress => __('app.status_in_progress'),
            self::Completed => __('app.status_completed'),
            self::Cancelled => __('app.status_cancelled'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
