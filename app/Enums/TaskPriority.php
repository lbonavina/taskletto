<?php

namespace App\Enums;

enum TaskPriority: string
{
    case Low    = 'low';
    case Medium = 'medium';
    case High   = 'high';
    case Urgent = 'urgent';

    public function label(): string
    {
        return match($this) {
            self::Low    => 'Baixa',
            self::Medium => 'Média',
            self::High   => 'Alta',
            self::Urgent => 'Urgente',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
