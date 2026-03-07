<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->group(function () {
    Route::apiResource('tasks', TaskController::class);
    Route::patch('tasks/{task}/complete', [TaskController::class , 'complete'])->name('tasks.complete');
    Route::patch('tasks/{task}/reopen', [TaskController::class , 'reopen'])->name('tasks.reopen');
    Route::get('tasks-stats', [TaskController::class , 'stats'])->name('tasks.stats');
});