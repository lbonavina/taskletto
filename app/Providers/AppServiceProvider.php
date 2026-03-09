<?php

namespace App\Providers;

use App\Models\Task;
use App\Observers\TaskObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // Register the Task observer for history tracking
        Task::observe(TaskObserver::class);

        // Compartilha overdueCount com todas as views que usam o layout
        View::composer('layouts.app', function ($view) {
            $view->with('overdueCount', Task::overdue()->count());
        });
    }
}