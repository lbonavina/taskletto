<?php

namespace App\Http\Controllers\Web;

use App\Enums\TaskRecurrence;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Task;
use App\Models\TaskTimeLog;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $total         = Task::count();
        $byStatus      = Task::selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status');
        $overdue       = Task::overdue()->count();
        $completionRate = $total > 0 ? round(($byStatus->get('completed', 0) / $total) * 100) : 0;

        // ── Last 7 days activity (1 query instead of 14) ─────────────────────
        $startOfWeek = now()->subDays(6)->startOfDay();

        $createdByDay = Task::selectRaw("DATE(created_at) as day, COUNT(*) as count")
            ->where('created_at', '>=', $startOfWeek)
            ->groupByRaw("DATE(created_at)")
            ->pluck('count', 'day');

        $completedByDay = Task::selectRaw("DATE(completed_at) as day, COUNT(*) as count")
            ->where('completed_at', '>=', $startOfWeek)
            ->whereNotNull('completed_at')
            ->groupByRaw("DATE(completed_at)")
            ->pluck('count', 'day');

        $days = collect(range(6, 0))->map(function ($daysAgo) use ($createdByDay, $completedByDay) {
            $date    = now()->subDays($daysAgo);
            $dateStr = $date->toDateString();
            return [
                'date'      => $date->format('d/m'),
                'day'       => $date->locale('pt_BR')->isoFormat('ddd'),
                'created'   => $createdByDay->get($dateStr, 0),
                'completed' => $completedByDay->get($dateStr, 0),
            ];
        });

        // ── Urgent & overdue tasks (max 5) ───────────────────────────────────
        $urgentTasks = Task::with('category')
            ->whereNotIn('status', [TaskStatus::Completed->value, TaskStatus::Cancelled->value])
            ->orderByRaw("CASE WHEN due_date < ? THEN 0 ELSE 1 END", [now()])
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 0 WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END")
            ->orderBy('due_date')
            ->limit(3)
            ->get();

        // ── Tasks due today ──────────────────────────────────────────────────
        $todayTasks = Task::with('category')
            ->whereNotIn('status', [TaskStatus::Completed->value, TaskStatus::Cancelled->value])
            ->whereDate('due_date', today())
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 0 WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END")
            ->limit(3)
            ->get();

        // ── Recent notes (max 5) ─────────────────────────────────────────────
        $recentNotes = Note::orderByDesc('updated_at')->limit(3)->get();

        // ── In-progress tasks (max 5) ────────────────────────────────────────
        $inProgressTasks = Task::where('status', TaskStatus::InProgress->value)
            ->orderBy('due_date')
            ->limit(3)
            ->get();

        // ── Category breakdown ───────────────────────────────────────────────
        $categories = Category::withCount('tasks')
            ->get()
            ->filter(fn($c) => $c->tasks_count > 0)
            ->sortByDesc('tasks_count')
            ->take(5);

        // ── Notes count ──────────────────────────────────────────────────────
        $notesStats = Note::selectRaw('COUNT(*) as total, SUM(pinned) as pinned')->first();
        $totalNotes  = (int) $notesStats->total;
        $pinnedNotes = (int) $notesStats->pinned;

        // ── Tracked seconds today (cross-compatible) ────────
        // Uses COALESCE so open (running) timers count up to now().
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        
        if ($isSqlite) {
            $trackedSql = "SUM(CAST(strftime('%s', COALESCE(ended_at, datetime('now'))) AS INTEGER) - CAST(strftime('%s', started_at) AS INTEGER)) as total_seconds";
        } else {
            $trackedSql = "SUM(TIMESTAMPDIFF(SECOND, started_at, COALESCE(ended_at, NOW()))) as total_seconds";
        }

        $trackedToday = (int) TaskTimeLog::whereDate('started_at', today())
            ->selectRaw($trackedSql)
            ->value('total_seconds');

        // ── Productivity streak (1 query instead of up to 366) ───────────────
        // Fetches all days with at least 1 completion, then counts the
        // unbroken streak backwards from today in PHP — no per-day queries.
        $completionDates = Task::selectRaw("DATE(completed_at) as day")
            ->whereNotNull('completed_at')
            ->groupByRaw("DATE(completed_at)")
            ->orderByRaw("DATE(completed_at) DESC")
            ->pluck('day')
            ->flip(); // keyed by date string for O(1) lookup

        $streak = 0;
        for ($i = 0; $i <= 365; $i++) {
            $dateStr = now()->subDays($i)->toDateString();
            if ($completionDates->has($dateStr)) {
                $streak++;
            } else {
                break;
            }
        }

        // ── Active recurring tasks count ─────────────────────────────────────
        $recurringCount = Task::whereNotIn('status', [TaskStatus::Completed->value, TaskStatus::Cancelled->value])
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
