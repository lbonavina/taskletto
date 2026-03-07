<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Note::query();

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%{$term}%")
            ->orWhere('content', 'like', "%{$term}%"));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('color')) {
            $query->where('color', $request->color);
        }

        $sort = $request->get('sort', 'updated_desc');
        match ($sort) {
                'updated_asc' => $query->orderBy('updated_at', 'asc'),
                'created_desc' => $query->orderBy('created_at', 'desc'),
                'created_asc' => $query->orderBy('created_at', 'asc'),
                'title_asc' => $query->orderBy('title', 'asc'),
                'title_desc' => $query->orderBy('title', 'desc'),
                default => $query->orderBy('updated_at', 'desc'),
            };

        $pinned = (clone $query)->where('pinned', true)->get();
        $others = (clone $query)->where('pinned', false)->get();
        $categories = Category::orderBy('name')->get();
        $overdueCount = Task::overdue()->count();

        // AJAX: return JSON for live search
        if ($request->expectsJson() || $request->ajax()) {
            $mapNote = fn($note) => [
            'id' => $note->id,
            'title' => $note->title ?: 'Sem título',
            'excerpt' => $note->excerpt() ?: 'Nota em branco…',
            'color' => $note->color,
            'pinned' => $note->pinned,
            'category' => $note->category,
            'updated_at' => $note->updated_at->diffForHumans(),
            ];
            return response()->json([
                'pinned' => $pinned->map($mapNote)->values(),
                'others' => $others->map($mapNote)->values(),
                'total' => $pinned->count() + $others->count(),
            ]);
        }

        return view('notes.index', compact('pinned', 'others', 'categories', 'overdueCount'));
    }

    public function store(Request $request): JsonResponse
    {
        $note = Note::create([
            'title' => 'Sem título',
            'content' => '',
            'color' => '#ff914d',
            'pinned' => false,
            'category' => null,
        ]);

        return response()->json(['id' => $note->id]);
    }

    public function show(Note $note)
    {
        $categories = Category::orderBy('name')->get();
        $overdueCount = Task::overdue()->count();
        return view('notes.show', compact('note', 'categories', 'overdueCount'));
    }

    public function update(Request $request, Note $note): JsonResponse
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|nullable|string',
            'color' => 'sometimes|string|regex:/^#[0-9a-fA-F]{6}$/',
            'pinned' => 'sometimes|boolean',
            'category' => 'sometimes|nullable|string|max:100',
        ]);

        $note->update($data);

        return response()->json(['ok' => true, 'updated_at' => $note->updated_at->format('d/m/Y H:i')]);
    }

    public function destroy(Note $note): JsonResponse
    {
        $note->delete();
        return response()->json(['ok' => true]);
    }
}