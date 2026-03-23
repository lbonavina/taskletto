<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\Note;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskTimeLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GistSyncService
{
    private const EXPORT_VERSION = '1.3';
    private const GIST_FILENAME  = 'taskletto-sync.json';
    private const API_BASE       = 'https://api.github.com';

    // ── Config helpers ────────────────────────────────────────────────────────

    public static function token(): ?string
    {
        return AppSetting::get('gist_token');
    }

    public static function gistId(): ?string
    {
        return AppSetting::get('gist_id');
    }

    public static function isConfigured(): bool
    {
        return !empty(self::token());
    }

    public static function lastSyncAt(): ?string
    {
        return AppSetting::get('gist_last_sync_at');
    }

    public static function lastSyncStatus(): string
    {
        return AppSetting::get('gist_last_sync_status', 'never');
    }

    // ── HTTP client ───────────────────────────────────────────────────────────

    private function http(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withToken(self::token())
            ->withHeaders([
                'Accept'     => 'application/vnd.github+json',
                'User-Agent' => 'Taskletto-App',
            ])
            ->timeout(20);
    }

    // ── Push (local → Gist) ───────────────────────────────────────────────────

    /**
     * Push the current local database to the Gist.
     * Creates a new Gist if none is configured yet.
     *
     * @return array{ok: bool, message: string, gist_id: ?string}
     */
    public function push(): array
    {
        if (!self::isConfigured()) {
            return ['ok' => false, 'message' => 'Token do GitHub não configurado.', 'gist_id' => null];
        }

        $payload = json_encode($this->buildPayload(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $gistId = self::gistId();

        if ($gistId) {
            // Update existing Gist
            $response = $this->http()->patch(self::API_BASE . "/gists/{$gistId}", [
                'files' => [self::GIST_FILENAME => ['content' => $payload]],
            ]);
        } else {
            // Create new secret Gist
            $response = $this->http()->post(self::API_BASE . '/gists', [
                'description' => 'Taskletto Sync — gerado automaticamente',
                'public'      => false,
                'files'       => [self::GIST_FILENAME => ['content' => $payload]],
            ]);
        }

        if ($response->successful()) {
            $newGistId = $response->json('id');
            AppSetting::set('gist_id', $newGistId);
            $this->recordSyncResult('ok');
            return ['ok' => true, 'message' => 'Dados enviados ao Gist com sucesso.', 'gist_id' => $newGistId];
        }

        $error = $response->json('message') ?? 'Erro desconhecido da API do GitHub.';
        $this->recordSyncResult('error', $error);
        Log::error("[GistSync] Push failed: {$error} (HTTP {$response->status()})");
        return ['ok' => false, 'message' => "Erro ao enviar: {$error}", 'gist_id' => $gistId];
    }

    // ── Pull + Merge (Gist → local) ───────────────────────────────────────────

    /**
     * Pull data from the Gist and merge intelligently with local data.
     * Winner per item = whichever has the most recent updated_at.
     *
     * @return array{ok: bool, message: string, stats: array}
     */
    public function pull(): array
    {
        if (!self::isConfigured()) {
            return ['ok' => false, 'message' => 'Token não configurado.', 'stats' => []];
        }

        $gistId = self::gistId();
        if (!$gistId) {
            return ['ok' => false, 'message' => 'Nenhum Gist vinculado. Faça um Push primeiro.', 'stats' => []];
        }

        $response = $this->http()->get(self::API_BASE . "/gists/{$gistId}");

        if (!$response->successful()) {
            $error = $response->json('message') ?? 'Erro ao buscar Gist.';
            $this->recordSyncResult('error', $error);
            return ['ok' => false, 'message' => $error, 'stats' => []];
        }

        $rawContent = $response->json("files." . self::GIST_FILENAME . ".content");
        if (!$rawContent) {
            return ['ok' => false, 'message' => 'Arquivo de sync não encontrado no Gist.', 'stats' => []];
        }

        $data = json_decode($rawContent, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['tasks'])) {
            return ['ok' => false, 'message' => 'Arquivo de sync corrompido ou inválido.', 'stats' => []];
        }

        $stats = $this->merge($data);
        $this->recordSyncResult('ok');

        return [
            'ok'      => true,
            'message' => "Merge concluído: {$stats['tasks_merged']} tasks, {$stats['notes_merged']} notas, {$stats['categories_merged']} categorias.",
            'stats'   => $stats,
        ];
    }

    // ── Full sync: push then pull ─────────────────────────────────────────────

    public function sync(): array
    {
        $push = $this->push();
        if (!$push['ok']) {
            return $push;
        }
        return $this->pull();
    }

    // ── Merge logic ───────────────────────────────────────────────────────────

    /**
     * Merge remote data into local DB using updated_at as the conflict resolver.
     * For each entity: if remote is newer → upsert locally; if local is newer → keep local.
     * New remote items (not found locally by a stable ID-independent key) are inserted.
     */
    private function merge(array $data): array
    {
        $stats = [
            'tasks_merged'      => 0,
            'notes_merged'      => 0,
            'categories_merged' => 0,
        ];

        DB::transaction(function () use ($data, &$stats) {

            // ── 1. Categories ─────────────────────────────────────────────────
            // Match by name (stable across machines)
            $categoryIdMap = []; // remote_id → local_id
            foreach ($data['categories'] ?? [] as $rc) {
                $local = Category::where('name', $rc['name'])->first();
                $remoteUpdated = Carbon::parse($rc['updated_at'] ?? $rc['created_at'] ?? now());

                if ($local) {
                    $categoryIdMap[$rc['id']] = $local->id;
                    if ($remoteUpdated->gt($local->updated_at)) {
                        $local->update([
                            'color'       => $rc['color'] ?? $local->color,
                            'icon'        => $rc['icon'] ?? $local->icon,
                            'description' => $rc['description'] ?? $local->description,
                        ]);
                        $stats['categories_merged']++;
                    }
                } else {
                    $new = Category::create([
                        'name'        => $rc['name'],
                        'color'       => $rc['color'] ?? '#ff914d',
                        'icon'        => $rc['icon'] ?? null,
                        'description' => $rc['description'] ?? null,
                    ]);
                    $categoryIdMap[$rc['id']] = $new->id;
                    $stats['categories_merged']++;
                }
            }

            // ── 2. Tasks ──────────────────────────────────────────────────────
            // Match by title + created_at (stable fingerprint)
            $taskIdMap = [];
            foreach ($data['tasks'] ?? [] as $rt) {
                $fingerprint  = $rt['title'];
                $remoteCreated = Carbon::parse($rt['created_at'] ?? now());
                $remoteUpdated = Carbon::parse($rt['updated_at'] ?? $rt['created_at'] ?? now());

                // Find local task by title + approximate created_at (within 5 seconds)
                $local = Task::where('title', $fingerprint)
                    ->whereBetween('created_at', [
                        $remoteCreated->copy()->subSeconds(5),
                        $remoteCreated->copy()->addSeconds(5),
                    ])
                    ->withTrashed()
                    ->first();

                $resolvedCategoryId = isset($rt['category_id'])
                    ? ($categoryIdMap[$rt['category_id']] ?? null)
                    : null;

                if ($local) {
                    $taskIdMap[$rt['id']] = $local->id;
                    if ($remoteUpdated->gt($local->updated_at)) {
                        $local->withoutObservers(fn() => $local->update([
                            'title'              => $rt['title'],
                            'description'        => $rt['description'] ?? $local->description,
                            'status'             => $rt['status'] ?? $local->status,
                            'priority'           => $rt['priority'] ?? $local->priority,
                            'category_id'        => $resolvedCategoryId ?? $local->category_id,
                            'due_date'           => $rt['due_date'] ?? $local->due_date,
                            'completed_at'       => $rt['completed_at'] ?? $local->completed_at,
                            'recurrence'         => $rt['recurrence'] ?? $local->recurrence,
                            'recurrence_ends_at' => $rt['recurrence_ends_at'] ?? $local->recurrence_ends_at,
                            'estimated_minutes'  => $rt['estimated_minutes'] ?? $local->estimated_minutes,
                            'tracked_seconds'    => max((int)($rt['tracked_seconds'] ?? 0), $local->tracked_seconds),
                            'sort_order'         => $rt['sort_order'] ?? $local->sort_order,
                        ]));
                        $stats['tasks_merged']++;
                    }
                } else {
                    $new = Task::withoutObservers(fn() => Task::create([
                        'title'              => $rt['title'],
                        'description'        => $rt['description'] ?? null,
                        'status'             => $rt['status'] ?? 'pending',
                        'priority'           => $rt['priority'] ?? 'medium',
                        'category_id'        => $resolvedCategoryId,
                        'due_date'           => $rt['due_date'] ?? null,
                        'completed_at'       => $rt['completed_at'] ?? null,
                        'recurrence'         => $rt['recurrence'] ?? 'none',
                        'recurrence_ends_at' => $rt['recurrence_ends_at'] ?? null,
                        'estimated_minutes'  => $rt['estimated_minutes'] ?? null,
                        'tracked_seconds'    => (int)($rt['tracked_seconds'] ?? 0),
                        'sort_order'         => $rt['sort_order'] ?? 0,
                        'created_at'         => $remoteCreated,
                        'updated_at'         => $remoteUpdated,
                    ]));
                    $taskIdMap[$rt['id']] = $new->id;
                    $stats['tasks_merged']++;

                    // Comments for new tasks
                    foreach ($rt['comments'] ?? [] as $rc2) {
                        $new->comments()->create([
                            'body'       => $rc2['body'],
                            'created_at' => Carbon::parse($rc2['created_at'] ?? now()),
                            'updated_at' => Carbon::parse($rc2['updated_at'] ?? now()),
                        ]);
                    }
                }
            }

            // ── 3. Notes ──────────────────────────────────────────────────────
            // Match by title + created_at
            foreach ($data['notes'] ?? [] as $rn) {
                $remoteCreated = Carbon::parse($rn['created_at'] ?? now());
                $remoteUpdated = Carbon::parse($rn['updated_at'] ?? $rn['created_at'] ?? now());

                $local = Note::where('title', $rn['title'])
                    ->whereBetween('created_at', [
                        $remoteCreated->copy()->subSeconds(5),
                        $remoteCreated->copy()->addSeconds(5),
                    ])
                    ->withTrashed()
                    ->first();

                if ($local) {
                    if ($remoteUpdated->gt($local->updated_at)) {
                        $local->update([
                            'title'   => $rn['title'] ?? $local->title,
                            'content' => $rn['content'] ?? $local->content,
                            'color'   => $rn['color'] ?? $local->color,
                            'pinned'  => $rn['pinned'] ?? $local->pinned,
                            'tags'    => $rn['tags'] ?? $local->tags,
                        ]);
                        $stats['notes_merged']++;
                    }
                } else {
                    Note::create([
                        'title'      => $rn['title'] ?? null,
                        'content'    => $rn['content'] ?? null,
                        'color'      => $rn['color'] ?? '#ff914d',
                        'pinned'     => $rn['pinned'] ?? false,
                        'tags'       => $rn['tags'] ?? null,
                        'created_at' => $remoteCreated,
                        'updated_at' => $remoteUpdated,
                    ]);
                    $stats['notes_merged']++;
                }
            }
        });

        return $stats;
    }

    // ── Payload builder ───────────────────────────────────────────────────────

    private function buildPayload(): array
    {
        return [
            'meta' => [
                'version'    => self::EXPORT_VERSION,
                'synced_at'  => now()->toIso8601String(),
                'app'        => 'Taskletto',
            ],
            'categories' => Category::orderBy('id')->get()->map(fn($c) => [
                'id'          => $c->id,
                'name'        => $c->name,
                'color'       => $c->color,
                'icon'        => $c->icon,
                'description' => $c->description,
                'created_at'  => $c->created_at?->toIso8601String(),
                'updated_at'  => $c->updated_at?->toIso8601String(),
            ])->values(),

            'tasks' => Task::with(['comments', 'timeLogs'])->withTrashed()->orderBy('id')->get()->map(fn($t) => [
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
                'updated_at'         => $t->updated_at?->toIso8601String(),
                'deleted_at'         => $t->deleted_at?->toIso8601String(),
                'comments'           => $t->comments->map(fn($c) => [
                    'body'       => $c->body,
                    'created_at' => $c->created_at?->toIso8601String(),
                    'updated_at' => $c->updated_at?->toIso8601String(),
                ])->values(),
            ])->values(),

            'notes' => Note::withTrashed()->orderBy('id')->get()->map(fn($n) => [
                'id'         => $n->id,
                'title'      => $n->title,
                'content'    => $n->content,
                'color'      => $n->color,
                'pinned'     => $n->pinned,
                'tags'       => $n->tags,
                'created_at' => $n->created_at?->toIso8601String(),
                'updated_at' => $n->updated_at?->toIso8601String(),
            ])->values(),
        ];
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function recordSyncResult(string $status, ?string $error = null): void
    {
        AppSetting::set('gist_last_sync_at', now()->toIso8601String());
        AppSetting::set('gist_last_sync_status', $status);
        if ($error) {
            AppSetting::set('gist_last_sync_error', $error);
        }
    }
}
