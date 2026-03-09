<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    private const PER_PAGE = 4;

    public function index(Request $request, Task $task): JsonResponse
    {
        $paginator = $task->comments()
            ->latest()
            ->paginate(self::PER_PAGE);

        return response()->json([
            'data' => $paginator->map(fn($c) => $this->format($c)),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'total' => $paginator->total(),
        ]);
    }

    public function store(Request $request, Task $task): JsonResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        $comment = $task->comments()->create($data);

        return response()->json($this->format($comment), 201);
    }

    public function update(Request $request, Task $task, TaskComment $comment): JsonResponse
    {
        abort_if($comment->task_id !== $task->id, 404);

        $data = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        $comment->update($data);

        return response()->json($this->format($comment));
    }

    public function destroy(Task $task, TaskComment $comment): JsonResponse
    {
        abort_if($comment->task_id !== $task->id, 404);

        $comment->delete();

        return response()->json(['deleted' => true]);
    }

    private function format(TaskComment $comment): array
    {
        return [
            'id' => $comment->id,
            'body' => $comment->body,
            'created_at' => $comment->created_at->format('d/m/Y H:i'),
            'updated_at' => $comment->updated_at->format('d/m/Y H:i'),
            'edited' => $comment->updated_at->gt($comment->created_at),
        ];
    }
}