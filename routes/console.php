<?php

use Illuminate\Support\Facades\Schedule;

/**
 * Notificações desktop para tarefas vencendo hoje e atrasadas.
 * Disparado a cada hora enquanto o app NativePHP estiver aberto.
 */
Schedule::command('tasks:notify-due')->hourly();
