<?php

namespace App\Providers;

use App\Models\Task;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // Compartilha overdueCount com todas as views que usam o layout
        View::composer('layouts.app', function ($view) {
            $view->with('overdueCount', Task::overdue()->count());
        });
    }
}