<?php

namespace App\Providers;

use Native\Desktop\Facades\Window;
use Native\Desktop\Facades\MenuBar;
use Native\Desktop\Contracts\ProvidesPhpIni;
use Native\Desktop\Events\Windows\WindowMinimized;
use Native\Desktop\Events\MenuBar\MenuBarClicked;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    public function boot(): void
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable $e) {
            Log::error('[NativePHP] migrate falhou no boot: ' . $e->getMessage());
        }

        Window::open('main')
            ->title('Taskletto - Gerenciador de Tarefas e Notas')
            ->width(1280)
            ->height(800)
            ->minWidth(960)
            ->minHeight(600)
            ->hideMenu()
            ->closable(true)
            ->showDevTools(false);
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
            $exe = dirname(base_path(), 3) . DIRECTORY_SEPARATOR . 'Taskletto.exe';
            Log::info('Taskletto autostart path: ' . $exe);
            exec('reg add "HKEY_CURRENT_USER\Software\Microsoft\Windows\CurrentVersion\Run" /v Taskletto /t REG_SZ /d "' . $exe . '" /f 2>NUL');
        }
    }

    public function phpIni(): array
    {
        return [];
    }
}