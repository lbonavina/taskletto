<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Note;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskTimeLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DataPortabilityController extends Controller
{
    private const EXPORT_VERSION = '1.2';

    // ── Export ────────────────────────────────────────────────────────────────

    public function export(): Response
    {
        $payload = [
            'meta' => [
                'version'    => self::EXPORT_VERSION,
                'exported_at' => now()->toIso8601String(),
                'app'        => 'Taskletto',
            ],
            'categories' => Category::orderBy('id')->get()->map(fn($c) => [
                'id'          => $c->id,
                'name'        => $c->name,
                'color'       => $c->color,
                'icon'        => $c->icon,
                'description' => $c->description,
                'created_at'  => $c->created_at?->toIso8601String(),
            ])->values(),

            'tasks' => Task::with(['comments', 'timeLogs'])->orderBy('id')->get()->map(fn($t) => [
                'id'                 => $t->id,
                'title'              => $t->title,
                'description'        => $t->description,
                'status'             => $t->status->value,
                'priority'           => $t->priority->value,
                'category_id'        => $t->category_id,
                'due_date'           => $t->due_date?->toDateString(),
                'completed_at'       => $t->completed_at?->toIso8601String(),
                'recurrence'         => $t->recurrence->value,
                'recurrence_ends_at' => $t->recurrence_ends_at?->toDateString(),
                'estimated_minutes'  => $t->estimated_minutes,
                'tracked_seconds'    => $t->tracked_seconds,
                'sort_order'         => $t->sort_order,
                'created_at'         => $t->created_at?->toIso8601String(),
                'comments'           => $t->comments->map(fn($c) => [
                    'id'         => $c->id,
                    'body'       => $c->body,
                    'created_at' => $c->created_at?->toIso8601String(),
                    'updated_at' => $c->updated_at?->toIso8601String(),
                ])->values(),
                'time_logs' => $t->timeLogs->map(fn($l) => [
                    'started_at' => $l->started_at?->toIso8601String(),
                    'ended_at'   => $l->ended_at?->toIso8601String(),
                ])->values(),
            ])->values(),

            'notes' => Note::orderBy('id')->get()->map(fn($n) => [
                'id'         => $n->id,
                'title'      => $n->title,
                'content'    => $n->content,
                'color'      => $n->color,
                'pinned'     => $n->pinned,
                'created_at' => $n->created_at?->toIso8601String(),
                'updated_at' => $n->updated_at?->toIso8601String(),
            ])->values(),
        ];

        $filename = 'taskletto-backup-' . now()->format('Y-m-d') . '.json';
        $json     = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return response($json, 200, [
            'Content-Type'        => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ── Import ────────────────────────────────────────────────────────────────

    public function import(Request $request): JsonResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimetypes:application/json,text/plain', 'max:20480']]);

        $raw = file_get_contents($request->file('file')->getRealPath());
        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! isset($data['meta'], $data['tasks'])) {
            return response()->json(['message' => 'Arquivo inválido ou corrompido.'], 422);
        }

        $version = $data['meta']['version'] ?? '0';
        if (version_compare($version, '1.0', '<')) {
            return response()->json(['message' => 'Versão do backup incompatível.'], 422);
        }

        DB::transaction(function () use ($data) {

            // ── Categories ───────────────────────────────────────────────────
            // Map old IDs → new IDs so task.category_id stays correct
            $categoryMap = [];
            foreach ($data['categories'] ?? [] as $c) {
                $existing = Category::where('name', $c['name'])->first();
                if ($existing) {
                    $categoryMap[$c['id']] = $existing->id;
                } else {
                    $new = Category::create([
                        'name'        => $c['name'],
                        'color'       => $c['color'] ?? '#ff914d',
                        'icon'        => $c['icon'] ?? null,
                        'description' => $c['description'] ?? null,
                    ]);
                    $categoryMap[$c['id']] = $new->id;
                }
            }

            // ── Tasks ────────────────────────────────────────────────────────
            foreach ($data['tasks'] ?? [] as $t) {
                $task = Task::create([
                    'title'              => $t['title'],
                    'description'        => $t['description'] ?? null,
                    'status'             => $t['status'] ?? 'pending',
                    'priority'           => $t['priority'] ?? 'medium',
                    'category_id'        => isset($t['category_id']) ? ($categoryMap[$t['category_id']] ?? null) : null,
                    'due_date'           => $t['due_date'] ?? null,
                    'completed_at'       => $t['completed_at'] ?? null,
                    'recurrence'         => $t['recurrence'] ?? 'none',
                    'recurrence_ends_at' => $t['recurrence_ends_at'] ?? null,
                    'estimated_minutes'  => $t['estimated_minutes'] ?? null,
                    'tracked_seconds'    => $t['tracked_seconds'] ?? 0,
                    'sort_order'         => $t['sort_order'] ?? 0,
                ]);

                // Comments
                foreach ($t['comments'] ?? [] as $c) {
                    $task->comments()->create([
                        'body'       => $c['body'],
                        'created_at' => $c['created_at'] ?? now(),
                        'updated_at' => $c['updated_at'] ?? now(),
                    ]);
                }

                // Time logs
                foreach ($t['time_logs'] ?? [] as $l) {
                    if (! empty($l['started_at'])) {
                        $task->timeLogs()->create([
                            'started_at' => $l['started_at'],
                            'ended_at'   => $l['ended_at'] ?? null,
                        ]);
                    }
                }
            }

            // ── Notes ────────────────────────────────────────────────────────
            foreach ($data['notes'] ?? [] as $n) {
                Note::create([
                    'title'      => $n['title'] ?? null,
                    'content'    => $n['content'] ?? null,
                    'color'      => $n['color'] ?? '#ff914d',
                    'pinned'     => $n['pinned'] ?? false,
                    'created_at' => $n['created_at'] ?? now(),
                    'updated_at' => $n['updated_at'] ?? now(),
                ]);
            }
        });

        $stats = [
            'categories' => count($data['categories'] ?? []),
            'tasks'      => count($data['tasks'] ?? []),
            'notes'      => count($data['notes'] ?? []),
        ];

        return response()->json([
            'message' => "Importação concluída: {$stats['tasks']} tasks, {$stats['notes']} notas, {$stats['categories']} categorias.",
            'stats'   => $stats,
        ]);
    }
}
