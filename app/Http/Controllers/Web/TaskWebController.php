<?php

namespace App\Http\Controllers\Web;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskWebController extends Controller
{
    public function index(Request $request): View
    {
        $query = Task::with('category');

        // Quick filters
        if ($request->filled('quick')) {
            $excludeFinished = [TaskStatus::Completed->value, TaskStatus::Cancelled->value];

            match ($request->quick) {
                'urgent'        => $query->where('priority', TaskPriority::Urgent->value)->whereNotIn('status', $excludeFinished),
                'today'         => $query->whereDate('due_date', today())->whereNotIn('status', $excludeFinished),
                'overdue'       => $query->overdue(),
                'recurring'     => $query->where('recurrence', '!=', 'none')->whereNotIn('status', $excludeFinished),
                'created_today' => $query->whereDate('created_at', today()),
                'created_week'  => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                'created_month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                default         => null,
            };
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = TaskStatus::tryFrom($request->status);
            if ($status) {
                $query->where('status', $status);
            }
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $priority = TaskPriority::tryFrom($request->priority);
            if ($priority) {
                $query->where('priority', $priority);
            }
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter: overdue only
        if ($request->boolean('overdue')) {
            $query->overdue();
        }

        // Filter: hide/show completed
        if ($request->boolean('hide_completed')) {
            $query->whereNotIn('status', [TaskStatus::Completed->value, TaskStatus::Cancelled->value]);
        }

        // Filter: date range (due_date)
        if ($request->filled('date_from')) {
            $query->whereDate('due_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('due_date', '<=', $request->date_to);
        }

        // Full-text search
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%"));
        }

        $tasks = $query
            ->orderBy('sort_order')
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->orderBy('due_date')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // ── Stats (5 queries instead of 8) ───────────────────────────────────

        // by_status covers: total, by_status, overdue proxy
        $byStatus = Task::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // by_priority filtered to active tasks only
        $byPriority = Task::selectRaw('priority, COUNT(*) as count')
            ->whereNotIn('status', [TaskStatus::Completed->value, TaskStatus::Cancelled->value])
            ->groupBy('priority')
            ->pluck('count', 'priority');

        // date-based counters in a single query
        $now        = now();
        $startWeek  = $now->copy()->startOfWeek()->toDateTimeString();
        $endWeek    = $now->copy()->endOfWeek()->toDateTimeString();

        $dateCounts = Task::selectRaw("
            SUM(CASE WHEN DATE(due_date) = DATE('now') AND status NOT IN (?, ?) THEN 1 ELSE 0 END) as due_today,
            SUM(CASE WHEN DATE(created_at) = DATE('now') THEN 1 ELSE 0 END)                        as created_today,
            SUM(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE 0 END)                            as created_week,
            SUM(CASE WHEN strftime('%Y-%m', created_at) = strftime('%Y-%m', 'now') THEN 1 ELSE 0 END) as created_month
        ", [TaskStatus::Completed->value, TaskStatus::Cancelled->value, $startWeek, $endWeek])->first();

        $categories = Category::withCount([
            'tasks',
            'tasks as active_tasks_count' => function ($q) {
                $q->whereNotIn('status', [TaskStatus::Completed->value, TaskStatus::Cancelled->value]);
            },
        ])->orderBy('name')->get();

        return view('tasks.index', [
            'tasks'      => $tasks,
            'categories' => $categories,
            'stats'      => [
                'total'         => $byStatus->sum(),
                'by_status'     => $byStatus->toArray(),
                'by_priority'   => $byPriority->toArray(),
                'overdue'       => Task::overdue()->count(),
                'due_today'     => (int) $dateCounts->due_today,
                'created_today' => (int) $dateCounts->created_today,
                'created_week'  => (int) $dateCounts->created_week,
                'created_month' => (int) $dateCounts->created_month,
            ],
        ]);
    }
}
