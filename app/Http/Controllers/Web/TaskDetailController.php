<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Task;
use Illuminate\View\View;

class TaskDetailController extends Controller
{
    public function show(Task $task): View
    {
        $task->load('histories', 'category', 'comments');
        $categories = Category::orderBy('name')->get();
        $overdueCount = Task::overdue()->count();
        return view('tasks.show', compact('task', 'categories', 'overdueCount'));
    }
}