<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Category;
use App\Models\Task;
use App\Services\NoteMarkdownExporter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Note::query();

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('content', 'like', "%{$term}%");
                try {
                    $q->orWhere('tags', 'like', "%{$term}%");
                } catch (\Exception $e) {}
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('color')) {
            $query->where('color', $request->color);
        }

        if ($request->filled('tag')) {
            try {
                $tag = $request->tag;
                $query->where(function ($q) use ($tag) {
                    $q->where('tags', $tag)
                      ->orWhere('tags', 'like', $tag . ',%')
                      ->orWhere('tags', 'like', '%,' . $tag)
                      ->orWhere('tags', 'like', '%,' . $tag . ',%');
                });
            } catch (\Exception $e) {
                // tags column may not exist yet
            }
        }

        $sort = $request->get('sort', 'updated_desc');
        match ($sort) {
            'updated_asc'  => $query->orderBy('updated_at', 'asc'),
            'created_desc' => $query->orderBy('created_at', 'desc'),
            'created_asc'  => $query->orderBy('created_at', 'asc'),
            'title_asc'    => $query->orderBy('title', 'asc'),
            'title_desc'   => $query->orderBy('title', 'desc'),
            default        => $query->orderBy('updated_at', 'desc'),
        };

        $pinned       = (clone $query)->where('pinned', true)->get();
        $others       = (clone $query)->where('pinned', false)->get();
        $categories   = Category::orderBy('name')->get();
        $allTags      = Note::allTags();
        $overdueCount = Task::overdue()->count();

        if ($request->expectsJson() || $request->ajax()) {
            $mapNote = fn($note) => [
                'id'         => $note->id,
                'title'      => $note->title ?: 'Sem título',
                'excerpt'    => $note->excerpt() ?: 'Nota em branco…',
                'color'      => $note->color,
                'pinned'     => $note->pinned,
                'category'   => $note->category,
                'tags'       => $note->tags_array,
                'updated_at' => $note->updated_at->diffForHumans(),
            ];
            return response()->json([
                'pinned'  => $pinned->map($mapNote)->values(),
                'others'  => $others->map($mapNote)->values(),
                'total'   => $pinned->count() + $others->count(),
                'allTags' => $allTags,
            ]);
        }

        return view('notes.index', compact('pinned', 'others', 'categories', 'allTags', 'overdueCount'));
    }

    public function store(Request $request): JsonResponse
    {
        $note = Note::create([
            'title' => '', 'content' => '', 'color' => '#ff914d',
            'pinned' => false, 'category' => null, 'tags' => null,
        ]);
        return response()->json(['id' => $note->id]);
    }

    public function show(Note $note)
    {
        $categories   = Category::orderBy('name')->get();
        $allTags      = Note::allTags();
        $overdueCount = Task::overdue()->count();
        return view('notes.show', compact('note', 'categories', 'allTags', 'overdueCount'));
    }

    public function update(Request $request, Note $note): JsonResponse
    {
        $data = $request->validate([
            'title'    => 'sometimes|nullable|string|max:255',
            'content'  => 'sometimes|nullable|string',
            'color'    => 'sometimes|string|regex:/^#[0-9a-fA-F]{6}$/',
            'pinned'   => 'sometimes|boolean',
            'category' => 'sometimes|nullable|string|max:100',
            'tags'     => 'sometimes|nullable|string|max:500',
        ]);
        $note->update($data);
        return response()->json([
            'ok'         => true,
            'updated_at' => $note->updated_at->format('d/m/Y H:i'),
            'tags_array' => $note->tags_array,
        ]);
    }

    public function export(Note $note, NoteMarkdownExporter $exporter): Response
    {
        $markdown = $exporter->toMarkdown($note);
        $title    = ($note->title && $note->title !== 'Sem título') ? $note->title : 'nota-' . $note->id;
        $filename = preg_replace('/\s+/', '-', trim(mb_strtolower(preg_replace('/[^a-zA-Z0-9\-_áéíóúãõâêîôûàèìòùç ]/u', '', $title))));
        $filename = trim($filename, '-') . '.md';
        return response($markdown, 200, [
            'Content-Type'        => 'text/markdown; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportPdf(Note $note)
    {
        return view('notes.pdf', compact('note'));
    }

    public function destroy(Note $note): JsonResponse
    {
        $note->delete();
        return response()->json(['ok' => true]);
    }
}