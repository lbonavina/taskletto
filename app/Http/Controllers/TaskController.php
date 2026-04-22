<?php

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\PlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Task::with('category');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->boolean('overdue')) {
            $query->overdue();
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }

        $perPage = min($request->integer('per_page', 15), 100);

        $tasks = $query->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->orderBy('due_date')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $user = Auth::user();

        if (! $user->canCreate('tasks')) {
            return response()->json([
                'message'  => app(PlanService::class)->limitMessage('tasks'),
                'upgrade'  => true,
                'limit'    => $user->plan()->limit('tasks'),
            ], 402);
        }

        $task = Task::create($request->validated());

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Task $task): TaskResource
    {
        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $task->update($request->validated());

        return new TaskResource($task->fresh());
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(null, 204);
    }

    public function complete(Task $task): JsonResponse
    {
        if ($task->isCompleted()) {
            return response()->json([
                'message' => 'Esta tarefa já está concluída.',
                'error' => 'already_completed',
            ], 409);
        }

        $task->complete();

        return response()->json([
            'message' => 'Tarefa marcada como concluída.',
            'data' => new TaskResource($task),
        ]);
    }

    public function reopen(Task $task): JsonResponse
    {
        if (!$task->isCompleted()) {
            return response()->json([
                'message' => 'Somente tarefas concluídas podem ser reabertas.',
                'error' => 'not_completed',
            ], 409);
        }

        $task->reopen();

        return response()->json([
            'message' => 'Tarefa reaberta com sucesso.',
            'data' => new TaskResource($task),
        ]);
    }

    public function updateEstimate(Request $request, Task $task): JsonResponse
    {
        $request->validate([
            'estimated_minutes' => ['required', 'integer', 'min:0', 'max:99999'],
        ]);

        $task->update(['estimated_minutes' => $request->estimated_minutes]);

        return response()->json([
            'message' => 'Estimativa atualizada.',
            'data' => new TaskResource($task->fresh()),
        ]);
    }

    public function stats(): JsonResponse
    {
        $total = Task::count();

        $byStatus = Task::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $byPriority = Task::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority');

        $overdue = Task::overdue()->count();

        $completionRate = $total > 0
            ? round(($byStatus->get('completed', 0) / $total) * 100, 2)
            : 0.0;

        return response()->json([
            'data' => [
                'total' => $total,
                'by_status' => $byStatus,
                'by_priority' => $byPriority,
                'overdue' => $overdue,
                'completion_rate' => $completionRate,
            ],
        ]);
    }
}
