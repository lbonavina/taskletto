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

        // Apply saved timezone from settings
        $timezone = \App\Models\AppSetting::get('timezone', config('app.timezone'));
        if ($timezone && in_array($timezone, timezone_identifiers_list())) {
            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);
        }

        // Compartilha overdueCount com todas as views que usam o layout
        View::composer('layouts.app', function ($view) {
            $view->with('overdueCount', Task::overdue()->count());
        });
    }
}