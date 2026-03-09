<?php

use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\TaskDetailController;
use App\Http\Controllers\Web\TaskSortController;
use App\Http\Controllers\Web\TaskWebController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', fn() => redirect()->route('dashboard'));

// Open external URLs in the system browser (NativePHP)
Route::get('/open-external', function (Request $request) {
    $url = $request->input('url');
    $allowed = ['https://github.com', 'https://www.linkedin.com', 'https://ko-fi.com'];
    $isAllowed = collect($allowed)->contains(fn($prefix) => str_starts_with($url, $prefix));
    if ($url && $isAllowed) {
        \Native\Laravel\Facades\Shell::openExternal($url);
    }
    return redirect()->back();
})->name('open-external');

Route::get('/dashboard', [DashboardController::class , 'index'])->name('dashboard');

Route::prefix('tasks')->name('tasks.')->group(function () {
    Route::get('/', [TaskWebController::class , 'index'])->name('index');
    Route::post('/sort', [TaskSortController::class , 'update'])->name('sort');
    Route::get('/{task}', [TaskDetailController::class , 'show'])->name('show');
});

Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class , 'index'])->name('index');
    Route::post('/', [CategoryController::class , 'store'])->name('store');
    Route::put('/{category}', [CategoryController::class , 'update'])->name('update');
    Route::delete('/{category}', [CategoryController::class , 'destroy'])->name('destroy');
});

Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
Route::post('/settings/locale', [SettingsController::class, 'setLocale'])->name('settings.locale');
Route::post('/settings/timezone', [SettingsController::class, 'setTimezone'])->name('settings.timezone');

Route::get('/settings/export', [\App\Http\Controllers\Web\DataPortabilityController::class, 'export'])->name('settings.export');
Route::post('/settings/import', [\App\Http\Controllers\Web\DataPortabilityController::class, 'import'])->name('settings.import');

Route::prefix('notes')->name('notes.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Web\NoteController::class , 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\Web\NoteController::class , 'store'])->name('store');
    Route::get('/{note}/export', [\App\Http\Controllers\Web\NoteController::class , 'export'])->name('export');
    Route::get('/{note}/export-pdf', [\App\Http\Controllers\Web\NoteController::class , 'exportPdf'])->name('export.pdf');
    Route::get('/{note}', [\App\Http\Controllers\Web\NoteController::class , 'show'])->name('show');
    Route::put('/{note}', [\App\Http\Controllers\Web\NoteController::class , 'update'])->name('update');
    Route::delete('/{note}', [\App\Http\Controllers\Web\NoteController::class , 'destroy'])->name('destroy');
});