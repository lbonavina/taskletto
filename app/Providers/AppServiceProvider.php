<?php

namespace App\Providers;

use App\Models\Task;
use App\Observers\TaskObserver;
use App\Services\PlanService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PlanService::class);
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Register the Task observer for history tracking
        Task::observe(TaskObserver::class);

        // Só aplica o timezone se o banco já estiver disponível (evita crash na primeira inicialização)
        try {
            $timezone = cache()->remember('app_timezone', 3600, fn () =>
                \App\Models\AppSetting::get('timezone', config('app.timezone'))
            );
            if ($timezone && in_array($timezone, timezone_identifiers_list())) {
                config(['app.timezone' => $timezone]);
                date_default_timezone_set($timezone);
            }
        } catch (\Throwable $e) {
            // Banco ainda não inicializado (primeira execução) — ignora silenciosamente
        }

        // Compartilha overdueCount com todas as views que usam o layout
        View::composer('layouts.app', function ($view) {
            try {
                $view->with('overdueCount', Task::overdue()->count());
            } catch (\Throwable $e) {
                $view->with('overdueCount', 0);
            }
        });
    }
}