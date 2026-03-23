<?php

namespace App\Providers;

use Native\Desktop\Facades\Window;
use Native\Desktop\Facades\MenuBar;
use Native\Desktop\Contracts\ProvidesPhpIni;
use Native\Desktop\Events\Windows\WindowMinimized;
use Native\Desktop\Events\Windows\WindowClosed;
use Native\Desktop\Events\MenuBar\MenuBarClicked;
use Illuminate\Support\Facades\Event;

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
            ->rememberState()
            ->hideMenu()
            ->showDevTools(false);

        // Ícone na bandeja — PNG obrigatório no NativePHP v2 (tray.ico não funciona)
        // Certifique-se de que public/icons/tray.png existe (16x16 ou 32x32 px)
        MenuBar::create()
            ->icon(public_path('icons/tray.png'))
            ->tooltip('Taskletto')
            ->onlyShowContextMenu()
            ->showDockIcon(false);

        // Clique no ícone da bandeja → mostra/foca a janela principal
        Event::listen(MenuBarClicked::class, function () {
            Window::show('main');
        });

        // Minimizar → esconde para bandeja
        Event::listen(WindowMinimized::class, function () {
            Window::hide('main');
        });

        // Fechar (X) → esconde para bandeja em vez de encerrar o processo
        Event::listen(WindowClosed::class, function () {
            Window::hide('main');
        });
    }

    public static function isAutoStartEnabled(): bool
    {
        if (PHP_OS_FAMILY !== 'Windows') return false;
        exec('reg query "HKEY_CURRENT_USER\Software\Microsoft\Windows\CurrentVersion\Run" /v Taskletto 2>NUL', $out, $code);
        return $code === 0;
    }

    public static function toggleAutoStart(): void
    {
        if (PHP_OS_FAMILY !== 'Windows') return;

        if (self::isAutoStartEnabled()) {
            exec('reg delete "HKEY_CURRENT_USER\Software\Microsoft\Windows\CurrentVersion\Run" /v Taskletto /f 2>NUL');
        } else {
            $exe = realpath(base_path('../../..')) . DIRECTORY_SEPARATOR . 'Taskletto.exe';
            exec('reg add "HKEY_CURRENT_USER\Software\Microsoft\Windows\CurrentVersion\Run" /v Taskletto /t REG_SZ /d "' . $exe . '" /f 2>NUL');
        }
    }

    public function phpIni(): array
    {
        return [];
    }
}