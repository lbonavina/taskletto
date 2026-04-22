<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\WelcomeController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\Web\BillingController;
use App\Http\Controllers\Web\NoteShareController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\SubtaskController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\TaskDetailController;
use App\Http\Controllers\Web\TaskSortController;
use App\Http\Controllers\Web\TaskWebController;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Native\Desktop\Facades\Window;
use Illuminate\Http\Request;

// ── Root ──────────────────────────────────────────────────────────────────────
Route::get('/', function (Request $request) {
    $isElectron = str_contains($request->userAgent() ?? '', 'Electron');

    if ($isElectron) {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        $onboarded = AppSetting::get('onboarding_complete');
        return redirect($onboarded ? route('login') : route('welcome'));
    }

    return view('landing');
});

// ── Desktop onboarding (Electron only) ───────────────────────────────────────
Route::get('/welcome',        [WelcomeController::class, 'show'])->name('welcome');

// ── Auth: register / login / logout ──────────────────────────────────────────
Route::get( '/register', [RegisterController::class, 'showForm'])->name('register')->middleware('guest');
Route::post('/register', [RegisterController::class, 'store'])->middleware('guest');

Route::get( '/login',  [LoginController::class, 'showForm'])->name('login')->middleware('guest');
Route::post('/login',  [LoginController::class, 'store'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

// ── Password reset (Disabled for now) ─────────────────────────────────────────
// Route::get( '/forgot-password',        [PasswordResetController::class, 'showForgotForm'])->name('password.request')->middleware('guest');
// Route::post('/forgot-password',        [PasswordResetController::class, 'sendResetLink'])->name('password.email')->middleware('guest');
// Route::get( '/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset')->middleware('guest');
// Route::post('/reset-password',         [PasswordResetController::class, 'reset'])->name('password.update')->middleware('guest');

// ── OAuth ─────────────────────────────────────────────────────────────────────
Route::get('/auth/{provider}/redirect', [OAuthController::class, 'redirect'])->name('oauth.redirect');
Route::get('/auth/{provider}/callback', [OAuthController::class, 'callback'])->name('oauth.callback');

// ── Pricing (redirect to landing section) ───────────────────────────────────
Route::get('/pricing', fn() => redirect('/#pricing'))->name('pricing');

// ── Public note sharing ───────────────────────────────────────────────────────
Route::get('/share/{token}',        [ShareController::class, 'show'])->name('share.show');
Route::post('/share/{token}/gate',  [ShareController::class, 'gate'])->name('share.gate');

// ── AbacatePay webhook (public, no CSRF) ─────────────────────────────────────
Route::post('/webhook/abacatepay', [WebhookController::class, 'abacatepay'])
    ->name('webhook.abacatepay')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// ── Open external URLs in the system browser (NativePHP) ─────────────────────
Route::get('/open-external', function (Request $request) {
    $url     = $request->input('url');
    $allowed = ['https://github.com', 'https://www.linkedin.com', 'https://ko-fi.com'];
    $isAllowed = collect($allowed)->contains(fn($prefix) => str_starts_with($url, $prefix));
    if ($url && $isAllowed) {
        \Native\Desktop\Facades\Shell::openExternal($url);
    }
    return redirect()->back();
})->name('open-external');

// ── Protected routes (require authentication) ────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/window/hide', function () {
        Window::hide('main');
        return response()->noContent();
    });

    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/',    [TaskWebController::class,  'index'])->name('index');
        Route::post('/sort', [TaskSortController::class, 'update'])->name('sort');
        Route::get('/{task}', [TaskDetailController::class, 'show'])->name('show');

        Route::get('/{task}/subtasks',              [SubtaskController::class, 'index'])->name('subtasks.index');
        Route::post('/{task}/subtasks',             [SubtaskController::class, 'store'])->name('subtasks.store');
        Route::put('/{task}/subtasks/{subtask}',    [SubtaskController::class, 'update'])->name('subtasks.update');
        Route::delete('/{task}/subtasks/{subtask}', [SubtaskController::class, 'destroy'])->name('subtasks.destroy');
        Route::post('/{task}/subtasks/reorder',     [SubtaskController::class, 'reorder'])->name('subtasks.reorder');
    });

    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/',              [CategoryController::class, 'index'])->name('index');
        Route::post('/',             [CategoryController::class, 'store'])->name('store');
        Route::put('/{category}',    [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    Route::get('/settings',                [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/locale',        [SettingsController::class, 'setLocale'])->name('settings.locale');
    Route::post('/settings/timezone',      [SettingsController::class, 'setTimezone'])->name('settings.timezone');

    Route::get('/billing',                 [BillingController::class, 'index'])->name('billing');
    Route::post('/billing/checkout/{plan}',[BillingController::class, 'checkout'])->name('billing.checkout');
    Route::get('/billing/success',         [BillingController::class, 'success'])->name('billing.success');
    Route::post('/billing/cancel',         [BillingController::class, 'cancel'])->name('billing.cancel');

    Route::get('/profile',                 [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile',                 [ProfileController::class, 'updateInfo'])->name('profile.update');
    Route::put('/profile/password',        [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar',         [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar',       [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');


    Route::get('/native/autostart/toggle', function () {
        \App\Providers\NativeAppServiceProvider::toggleAutoStart();
        return response()->json(['enabled' => \App\Providers\NativeAppServiceProvider::isAutoStartEnabled()]);
    })->name('native.autostart.toggle');

    Route::prefix('notes')->name('notes.')->group(function () {
        Route::get('/',                [\App\Http\Controllers\Web\NoteController::class, 'index'])->name('index');
        Route::post('/',               [\App\Http\Controllers\Web\NoteController::class, 'store'])->name('store');
        Route::get('/{note}/export',     [\App\Http\Controllers\Web\NoteController::class, 'export'])->name('export');
        Route::get('/{note}/export-pdf', [\App\Http\Controllers\Web\NoteController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/{note}',            [\App\Http\Controllers\Web\NoteController::class, 'show'])->name('show');
        Route::put('/{note}',            [\App\Http\Controllers\Web\NoteController::class, 'update'])->name('update');
        Route::delete('/{note}',         [\App\Http\Controllers\Web\NoteController::class, 'destroy'])->name('destroy');
        Route::post('/{note}/share',     [NoteShareController::class, 'store'])->name('share.store');
        Route::delete('/{note}/share',   [NoteShareController::class, 'destroy'])->name('share.destroy');
        Route::post('/{note}/share/regenerate', [NoteShareController::class, 'regenerate'])->name('share.regenerate');
    });
});
