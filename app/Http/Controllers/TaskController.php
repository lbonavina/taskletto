<?php

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Info(
 *     title="Taskletto",
 *     version="1.3.0",
 *     description="API RESTful para gerenciamento de tarefas com suporte a prioridades, categorias e status.",
 *     @OA\Contact(
 *         email="seu@email.com",
 *         name="Seu Nome"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/api/v1",
 *     description="API v1"
 * )
 *
 * @OA\Tag(name="Tasks", description="Operações de gerenciamento de tarefas")
 *
 * @OA\Schema(
 *     schema="TaskStatus",
 *     type="string",
 *     enum={"pending", "in_progress", "completed", "cancelled"},
 *     example="pending"
 * )
 *
 * @OA\Schema(
 *     schema="TaskPriority",
 *     type="string",
 *     enum={"low", "medium", "high", "urgent"},
 *     example="medium"
 * )
 *
 * @OA\Schema(
 *     schema="Task",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Estudar Laravel"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Estudar os novos recursos do Laravel 11"),
 *     @OA\Property(property="status", type="object",
 *         @OA\Property(property="value", type="string", example="pending"),
 *         @OA\Property(property="label", type="string", example="Pendente")
 *     ),
 *     @OA\Property(property="priority", type="object",
 *         @OA\Property(property="value", type="string", example="high"),
 *         @OA\Property(property="label", type="string", example="Alta")
 *     ),
 *     @OA\Property(property="category", type="string", nullable=true, example="Estudos"),
 *     @OA\Property(property="due_date", type="string", format="date", nullable=true, example="2025-12-31"),
 *     @OA\Property(property="is_overdue", type="boolean", example=false),
 *     @OA\Property(property="completed_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="ValidationError",
 *     @OA\Property(property="message", type="string", example="The title field is required."),
 *     @OA\Property(property="errors", type="object",
 *         @OA\AdditionalProperties(
 *             type="array",
 *             @OA\Items(type="string")
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="NotFoundError",
 *     @OA\Property(property="message", type="string", example="Task not found."),
 *     @OA\Property(property="error", type="string", example="not_found")
 * )
 */
class TaskController extends Controller
{
    /**
     * @OA\Get(
     *     path="/tasks",
     *     tags={"Tasks"},
     *     summary="Listar todas as tarefas",
     *     description="Retorna uma lista paginada de tarefas com suporte a filtros.",
     *     @OA\Parameter(name="status", in="query", required=false, description="Filtrar por status",
     *         @OA\Schema(ref="#/components/schemas/TaskStatus")
     *     ),
     *     @OA\Parameter(name="priority", in="query", required=false, description="Filtrar por prioridade",
     *         @OA\Schema(ref="#/components/schemas/TaskPriority")
     *     ),
     *     @OA\Parameter(name="category", in="query", required=false, description="Filtrar por categoria",
     *         @OA\Schema(type="string", example="Trabalho")
     *     ),
     *     @OA\Parameter(name="overdue", in="query", required=false, description="Listar apenas tarefas atrasadas",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(name="search", in="query", required=false, description="Buscar por título ou descrição",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(name="per_page", in="query", required=false, description="Itens por página (padrão: 15)",
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tarefas retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Task")),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Task::with('category');

        // Filtro por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por prioridade
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filtro por categoria
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro: apenas atrasadas
        if ($request->boolean('overdue')) {
            $query->overdue();
        }

        // Busca textual
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

    /**
     * @OA\Post(
     *     path="/tasks",
     *     tags={"Tasks"},
     *     summary="Criar uma nova tarefa",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", minLength=3, maxLength=255, example="Comprar mantimentos"),
     *             @OA\Property(property="description", type="string", nullable=true, example="Leite, pão e ovos"),
     *             @OA\Property(property="status", ref="#/components/schemas/TaskStatus"),
     *             @OA\Property(property="priority", ref="#/components/schemas/TaskPriority"),
     *             @OA\Property(property="category", type="string", nullable=true, example="Pessoal"),
     *             @OA\Property(property="due_date", type="string", format="date", nullable=true, example="2025-12-31")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Tarefa criada com sucesso",
     *         @OA\JsonContent(@OA\Property(property="data", ref="#/components/schemas/Task"))
     *     ),
     *     @OA\Response(response=422, description="Erro de validação",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Buscar uma tarefa pelo ID",
     *     @OA\Parameter(name="id", in="path", required=true, description="ID da tarefa",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Tarefa encontrada",
     *         @OA\JsonContent(@OA\Property(property="data", ref="#/components/schemas/Task"))
     *     ),
     *     @OA\Response(response=404, description="Tarefa não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *     )
     * )
     */
    public function show(Task $task): TaskResource
    {
        return new TaskResource($task);
    }

    /**
     * @OA\Put(
     *     path="/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Atualizar uma tarefa",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", minLength=3, maxLength=255),
     *             @OA\Property(property="description", type="string", nullable=true),
     *             @OA\Property(property="status", ref="#/components/schemas/TaskStatus"),
     *             @OA\Property(property="priority", ref="#/components/schemas/TaskPriority"),
     *             @OA\Property(property="category", type="string", nullable=true),
     *             @OA\Property(property="due_date", type="string", format="date", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Tarefa atualizada",
     *         @OA\JsonContent(@OA\Property(property="data", ref="#/components/schemas/Task"))
     *     ),
     *     @OA\Response(response=404, description="Tarefa não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *     ),
     *     @OA\Response(response=422, description="Erro de validação",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $task->update($request->validated());

        return new TaskResource($task->fresh());
    }

    /**
     * @OA\Delete(
     *     path="/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Excluir uma tarefa",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Tarefa excluída com sucesso"),
     *     @OA\Response(response=404, description="Tarefa não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *     )
     * )
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(null, 204);
    }

    /**
     * @OA\Patch(
     *     path="/tasks/{id}/complete",
     *     tags={"Tasks"},
     *     summary="Marcar tarefa como concluída",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Tarefa marcada como concluída",
     *         @OA\JsonContent(@OA\Property(property="data", ref="#/components/schemas/Task"))
     *     ),
     *     @OA\Response(response=409, description="Tarefa já está concluída",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Esta tarefa já está concluída."),
     *             @OA\Property(property="error", type="string", example="already_completed")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Tarefa não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *     )
     * )
     */
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

    /**
     * @OA\Patch(
     *     path="/tasks/{id}/reopen",
     *     tags={"Tasks"},
     *     summary="Reabrir uma tarefa concluída",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Tarefa reaberta com sucesso",
     *         @OA\JsonContent(@OA\Property(property="data", ref="#/components/schemas/Task"))
     *     ),
     *     @OA\Response(response=409, description="Tarefa não está concluída",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Somente tarefas concluídas podem ser reabertas."),
     *             @OA\Property(property="error", type="string", example="not_completed")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Patch(
     *     path="/tasks/{id}/estimate",
     *     tags={"Tasks"},
     *     summary="Atualizar estimativa de tempo da tarefa",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"estimated_minutes"},
     *             @OA\Property(property="estimated_minutes", type="integer", minimum=0, maximum=99999, example=90)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Estimativa atualizada com sucesso",
     *         @OA\JsonContent(@OA\Property(property="data", ref="#/components/schemas/Task"))
     *     ),
     *     @OA\Response(response=404, description="Tarefa não encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *     ),
     *     @OA\Response(response=422, description="Erro de validação",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/tasks/stats",
     *     tags={"Tasks"},
     *     summary="Estatísticas gerais das tarefas",
     *     @OA\Response(
     *         response=200,
     *         description="Estatísticas retornadas com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="by_status", type="object"),
     *                 @OA\Property(property="by_priority", type="object"),
     *                 @OA\Property(property="overdue", type="integer"),
     *                 @OA\Property(property="completion_rate", type="number", format="float")
     *             )
     *         )
     *     )
     * )
     */
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
