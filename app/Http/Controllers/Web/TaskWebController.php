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
        $query = Task::query();

        // Quick filters
        if ($request->filled('quick')) {
            match ($request->quick) {
                    'urgent' => $query->where('priority', 'urgent')->whereNotIn('status', ['completed', 'cancelled']),
                    'today' => $query->whereDate('due_date', today())->whereNotIn('status', ['completed', 'cancelled']),
                    'overdue' => $query->overdue(),
                    default => null,
                };
        }

        // Advanced filters
        if ($request->filled('status'))
            $query->where('status', $request->status);
        if ($request->filled('priority'))
            $query->where('priority', $request->priority);
        if ($request->filled('category'))
            $query->byCategory($request->category);
        if ($request->boolean('overdue'))
            $query->overdue();

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

        $categories = Category::orderBy('name')->get();

        return view('tasks.index', [
            'tasks' => $tasks,
            'categories' => $categories,
            'stats' => [
                'total' => Task::count(),
                'by_status' => $byStatus->toArray(),
                'by_priority' => $byPriority->toArray(),
                'overdue' => Task::overdue()->count(),
                'due_today' => Task::whereDate('due_date', today())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            ],
        ]);
    }
}