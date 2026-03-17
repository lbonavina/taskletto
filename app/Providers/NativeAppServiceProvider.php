<?php

namespace App\Providers;

use Native\Laravel\Facades\Window;
use Native\Laravel\Facades\Schedule;
use Native\Laravel\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    public function boot(): void
    {
        Window::open()
            ->title('Taskletto - Gerenciador de tarefas e notas')
            ->width(1280)
            ->height(800)
            ->minWidth(960)
            ->minHeight(600)
            ->removeMenu()
            ->showDevTools(false);

        \Native\Laravel\Facades\Notification::new ()
            ->title('App iniciado!')
            ->message('NativePHP funcionando.')
            ->show();
    }

    public function phpIni(): array
    {
        return [];
    }
}