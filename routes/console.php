<?php

use Illuminate\Support\Facades\Schedule;

/**
 * Notificações desktop para tarefas vencendo hoje e atrasadas.
 * Disparado a cada hora enquanto o app NativePHP estiver aberto.
 */
Schedule::command('tasks:notify-due')->hourly();

/**
 * GitHub Gist auto-sync.
 * Roda a cada minuto — o próprio comando verifica se o intervalo
 * configurado pelo usuário (5, 10, 15, 30 ou 60 min) já passou.
 */
Schedule::command('gist:auto-sync')->everyMinute()->withoutOverlapping();
