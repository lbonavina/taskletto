<?php

namespace App\Enums;

enum Plan: string
{
    case Free = 'free';
    case Pro  = 'pro';

    public function label(): string
    {
        return match ($this) {
            Plan::Free => 'Free',
            Plan::Pro  => 'Pro',
        };
    }

    public function priceCents(string $period = 'monthly'): int
    {
        return match ($this) {
            Plan::Free => 0,
            Plan::Pro  => $period === 'annual' ? 11988 : 1499, // R$ 119,88/ano ou R$ 14,99/mês
        };
    }

    /** Price per month displayed to the user (annual = R$ 9,99, monthly = R$ 14,99). */
    public function monthlyDisplayCents(string $period = 'monthly'): int
    {
        return match ($this) {
            Plan::Free => 0,
            Plan::Pro  => $period === 'annual' ? 999 : 1499,
        };
    }

    public function priceFormatted(string $period = 'monthly'): string
    {
        return match ($this) {
            Plan::Free => 'Grátis',
            Plan::Pro  => $period === 'annual' ? 'R$ 9,99/mês' : 'R$ 14,99/mês',
        };
    }

    /** Limits: null = unlimited */
    public function limits(): array
    {
        return match ($this) {
            Plan::Free => [
                'tasks'      => 50,
                'notes'      => 5,
                'categories' => 5,
                'storage_mb' => 50,
            ],
            Plan::Pro => [
                'tasks'      => null,
                'notes'      => 50,
                'categories' => null,
                'storage_mb' => 3072,
            ],
        };
    }

    public function limit(string $resource): ?int
    {
        return $this->limits()[$resource] ?? null;
    }

    public function features(): array
    {
        return match ($this) {
            Plan::Free => [
                'Até 50 tarefas ativas',
                'Até 5 notas ricas',
                '50MB de armazenamento',
                'App Desktop + Acesso Web',
                'Subtarefas e prioridades',
            ],
            Plan::Pro => [
                'Tarefas e categorias ilimitadas',
                'Até 50 notas ricas',
                '3GB de armazenamento',
                'App Desktop + Acesso Web',
                'Suporte prioritário',
            ],
        };
    }
}
