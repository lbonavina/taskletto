<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskSortController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);
        foreach ($request->order as $position => $taskId) {
            Task::where('id', $taskId)->update(['sort_order' => $position]);
        }
        return response()->json(['message' => 'Ordem salva.']);
    }
}
