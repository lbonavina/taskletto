<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskCommentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->group(function () {
    Route::get('health', fn() => response()->json(['status' => 'ok']))->name('health');
    Route::get('tasks/stats', [TaskController::class, 'stats'])->name('tasks.stats');
    Route::apiResource('tasks', TaskController::class);
    Route::patch('tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::patch('tasks/{task}/reopen', [TaskController::class, 'reopen'])->name('tasks.reopen');
    Route::get('tasks/{task}/comments', [TaskCommentController::class, 'index'])->name('tasks.comments.index');
    Route::post('tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('tasks.comments.store');
    Route::patch('tasks/{task}/comments/{comment}', [TaskCommentController::class, 'update'])->name('tasks.comments.update');
    Route::delete('tasks/{task}/comments/{comment}', [TaskCommentController::class, 'destroy'])->name('tasks.comments.destroy');
});