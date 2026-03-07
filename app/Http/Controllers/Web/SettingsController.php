<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
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
}
