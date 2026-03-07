<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $overdueCount = Task::overdue()->count();
        $dbSize = file_exists(database_path('database.sqlite'))
            ? round(filesize(database_path('database.sqlite')) / 1024, 1) . ' KB'
            : 'MySQL';

        return view('settings', compact('overdueCount', 'dbSize'));
    }

    public function setLocale(Request $request)
    {
        $allowed = ['pt', 'en', 'es'];
        $locale  = $request->input('locale', 'pt');

        if (in_array($locale, $allowed)) {
            session(['locale' => $locale]);
        }

        return redirect()->route('settings')->with('locale_saved', true);
    }
}
