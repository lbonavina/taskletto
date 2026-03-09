<?php

namespace App\Enums;

enum TaskRecurrence: string
{
    case None    = 'none';
    case Daily   = 'daily';
    case Weekly  = 'weekly';
    case Monthly = 'monthly';

    public function label(): string
    {
        return match ($this) {
            self::None    => 'Sem recorrência',
            self::Daily   => 'Diária',
            self::Weekly  => 'Semanal',
            self::Monthly => 'Mensal',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
