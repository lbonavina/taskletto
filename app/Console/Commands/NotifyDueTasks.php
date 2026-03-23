<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Native\Desktop\Facades\Notification;

class NotifyDueTasks extends Command
{
    protected $signature = 'tasks:notify-due';
    protected $description = 'Envia notificações desktop para tarefas vencendo hoje e atrasadas.';

    public function handle(): int
    {
        $today = today();

        // ── Tarefas vencendo HOJE ─────────────────────────────────────────────
        $dueToday = Task::whereDate('due_date', $today)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();

        if ($dueToday > 0) {
            $body = $dueToday === 1
                ? 'Você tem 1 tarefa com vencimento hoje.'
                : "Você tem {$dueToday} tarefas com vencimento hoje.";

            Notification::new ()
                ->title('📅 Vence hoje')
                ->message($body)
                ->show();
        }

        // ── Tarefas ATRASADAS ─────────────────────────────────────────────────
        $overdue = Task::overdue()->count();

        if ($overdue > 0) {
            $body = $overdue === 1
                ? '1 tarefa está atrasada e ainda não foi concluída.'
                : "{$overdue} tarefas estão atrasadas e ainda não foram concluídas.";

            Notification::new ()
                ->title('⚠️ Tarefas atrasadas')
                ->message($body)
                ->show();
        }

        $this->info("Notificações enviadas — vencendo hoje: {$dueToday}, atrasadas: {$overdue}.");

        return self::SUCCESS;
    }
}