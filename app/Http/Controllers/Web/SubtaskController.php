<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskSubtask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
    /** List subtasks for a task */
    public function index(Task $task): JsonResponse
    {
        return response()->json(
            $task->subtasks()->orderBy('sort_order')->orderBy('id')->get()
        );
    }

    /** Create a new subtask */
    public function store(Request $request, Task $task): JsonResponse
    {
        $request->validate(['title' => 'required|string|max:255']);

        $subtask = $task->subtasks()->create([
            'title'      => $request->title,
            'sort_order' => $task->subtasks()->max('sort_order') + 1,
        ]);

        return response()->json($subtask, 201);
    }

    /** Toggle completed / update title */
    public function update(Request $request, Task $task, TaskSubtask $subtask): JsonResponse
    {
        $request->validate([
            'title'      => 'sometimes|string|max:255',
            'completed'  => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer',
        ]);

        $subtask->update($request->only(['title', 'completed', 'sort_order']));

        return response()->json($subtask->fresh());
    }

    /** Delete a subtask */
    public function destroy(Task $task, TaskSubtask $subtask): JsonResponse
    {
        $subtask->delete();
        return response()->json(['ok' => true]);
    }

    /** Reorder subtasks */
    public function reorder(Request $request, Task $task): JsonResponse
    {
        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);

        foreach ($request->order as $i => $id) {
            $task->subtasks()->where('id', $id)->update(['sort_order' => $i]);
        }

        return response()->json(['ok' => true]);
    }
}
