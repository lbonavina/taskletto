<?php

namespace App\Enums;

enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Urgent = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::Low => __('app.priority_low'),
            self::Medium => __('app.priority_medium'),
            self::High => __('app.priority_high'),
            self::Urgent => __('app.priority_urgent'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
