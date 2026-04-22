<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Task;
use App\Services\PlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories   = Category::withCount('tasks')->orderBy('name')->get();
        $overdueCount = Task::overdue()->count();
        return view('categories.index', compact('categories', 'overdueCount'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (! $user->canCreate('categories')) {
            return back()->with('limit_error', app(PlanService::class)->limitMessage('categories'));
        }

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->where('user_id', $user->id)],
            'color'       => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'icon'        => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);
        Category::create($data);
        return back()->with('success', 'Categoria criada com sucesso!');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->where('user_id', Auth::id())->ignore($category->id)],
            'color'       => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'icon'        => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);
        $category->update($data);
        return back()->with('success', 'Categoria atualizada!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Categoria excluída.');
    }
}
