<?php

namespace App\Http\Controllers\Web;

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
            match ($request->quick) {
                'urgent' => $query->where('priority', 'urgent')->whereNotIn('status', ['completed', 'cancelled']),
                'today' => $query->whereDate('due_date', today())->whereNotIn('status', ['completed', 'cancelled']),
                'overdue' => $query->overdue(),
                'recurring' => $query->where('recurrence', '!=', 'none')->whereNotIn('status', ['completed', 'cancelled']),
                'created_today' => $query->whereDate('created_at', today()),
                'created_week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                'created_month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                default => null,
            };
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = \App\Enums\TaskStatus::tryFrom($request->status);
            if ($status)
                $query->where('status', $status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $priority = \App\Enums\TaskPriority::tryFrom($request->priority);
            if ($priority)
                $query->where('priority', $priority);
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
            $query->whereNotIn('status', ['completed', 'cancelled']);
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

        $byStatus = Task::selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status');
        $byPriority = Task::selectRaw('priority, COUNT(*) as count')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->groupBy('priority')->pluck('count', 'priority');

        $categories = Category::withCount([
            'tasks',
            'tasks as active_tasks_count' => function ($q) {
                $q->whereNotIn('status', ['completed', 'cancelled']);
            }
        ])->orderBy('name')->get();

        return view('tasks.index', [
            'tasks' => $tasks,
            'categories' => $categories,
            'stats' => [
                'total' => Task::count(),
                'by_status' => $byStatus->toArray(),
                'by_priority' => $byPriority->toArray(),
                'overdue' => Task::overdue()->count(),
                'due_today' => Task::whereDate('due_date', today())
                    ->whereNotIn('status', ['completed', 'cancelled'])->count(),
                'created_today' => Task::whereDate('created_at', today())->count(),
                'created_week' => Task::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'created_month' => Task::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            ],
        ]);
    }
}