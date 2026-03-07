<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories  = Category::withCount('tasks')->orderBy('name')->get();
        $overdueCount = \App\Models\Task::overdue()->count();
        return view('categories.index', compact('categories', 'overdueCount'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name',
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
            'name'        => 'required|string|max:100|unique:categories,name,'.$category->id,
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
