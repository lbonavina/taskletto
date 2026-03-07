<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Pending    = 'pending';
    case InProgress = 'in_progress';
    case Completed  = 'completed';
    case Cancelled  = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending    => 'Pendente',
            self::InProgress => 'Em progresso',
            self::Completed  => 'Concluída',
            self::Cancelled  => 'Cancelada',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
