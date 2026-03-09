<?php

namespace App\Http\Controllers\Web;

use App\Enums\TaskRecurrence;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Task;
use App\Models\TaskTimeLog;
use App\Models\Category;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $total         = Task::count();
        $byStatus      = Task::selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status');
        $overdue       = Task::overdue()->count();
        $completionRate = $total > 0 ? round(($byStatus->get('completed', 0) / $total) * 100) : 0;

        // Last 7 days activity
        $days = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);
            return [
                'date'      => $date->format('d/m'),
                'day'       => $date->locale('pt_BR')->isoFormat('ddd'),
                'created'   => Task::whereDate('created_at', $date)->count(),
                'completed' => Task::whereDate('completed_at', $date)->count(),
            ];
        });

        // Urgent & overdue tasks (max 5)
        $urgentTasks = Task::with('category')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderByRaw("CASE WHEN due_date < datetime('now') THEN 0 ELSE 1 END")
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 0 WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END")
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Tasks due today
        $todayTasks = Task::with('category')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereDate('due_date', today())
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 0 WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END")
            ->limit(8)
            ->get();

        // Recent notes (max 5 — compact list now)
        $recentNotes = Note::orderByDesc('updated_at')->limit(5)->get();

        // In-progress tasks with time data (max 5)
        $inProgressTasks = Task::where('status', 'in_progress')
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Category breakdown
        $categories = Category::withCount('tasks')
            ->get()
            ->filter(fn($c) => $c->tasks_count > 0)
            ->sortByDesc('tasks_count')
            ->take(5);

        // Notes count
        $totalNotes  = Note::count();
        $pinnedNotes = Note::where('pinned', true)->count();

        // Tracked seconds today (sum of all completed + running logs today)
        $trackedToday = TaskTimeLog::whereDate('started_at', today())
            ->get()
            ->sum(fn($log) => $log->durationSeconds());

        // Productivity streak: consecutive days with at least 1 completed task
        $streak = 0;
        for ($i = 0; $i <= 365; $i++) {
            $date = now()->subDays($i)->toDateString();
            $completed = Task::whereDate('completed_at', $date)->count();
            if ($completed > 0) {
                $streak++;
            } else {
                break;
            }
        }

        // Active recurring tasks count
        $recurringCount = Task::whereNotIn('status', ['completed', 'cancelled'])
            ->where('recurrence', '!=', TaskRecurrence::None->value)
            ->count();

        return view('dashboard', compact(
            'total', 'byStatus', 'overdue', 'completionRate',
            'days', 'urgentTasks', 'todayTasks', 'recentNotes',
            'inProgressTasks', 'categories', 'totalNotes', 'pinnedNotes',
            'trackedToday', 'streak', 'recurringCount'
        ) + ['overdueCount' => $overdue]);
    }
}
