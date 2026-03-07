<?php

namespace App\Providers;

use Native\Laravel\Facades\Window;
use Native\Laravel\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    public function boot(): void
    {
        Window::open()
            ->title('Taskletto')
            ->width(1280)
            ->height(800)
            ->minWidth(960)
            ->minHeight(600)
            ->showDevTools(false);
    }

    public function phpIni(): array
    {
        return [];
    }
}