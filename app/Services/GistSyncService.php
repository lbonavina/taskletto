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

    public static function token(): ?string { return AppSetting::get('gist_token'); }
    public static function gistId(): ?string { return AppSetting::get('gist_id'); }
    public static function isConfigured(): bool { return !empty(self::token()); }
    public static function lastSyncAt(): ?string { return AppSetting::get('gist_last_sync_at'); }
    public static function lastSyncStatus(): string { return AppSetting::get('gist_last_sync_status', 'never'); }

    private function http(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withToken(self::token())
            ->withHeaders(['Accept' => 'application/vnd.github+json', 'User-Agent' => 'Taskletto-App'])
            ->timeout(20);
    }

    public function push(): array
    {
        Log::info('[GistSync][PUSH] Iniciando...');
        if (!self::isConfigured()) {
            Log::warning('[GistSync][PUSH] Token nao configurado.');
            return ['ok' => false, 'message' => 'Token do GitHub nao configurado.', 'gist_id' => null];
        }
        $payload = json_encode($this->buildPayload(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $gistId  = self::gistId();
        Log::info("[GistSync][PUSH] gist_id={$gistId}, payload_size=" . strlen($payload));
        if ($gistId) {
            $response = $this->http()->patch(self::API_BASE . "/gists/{$gistId}", [
                'files' => [self::GIST_FILENAME => ['content' => $payload]],
            ]);
        } else {
            $response = $this->http()->post(self::API_BASE . '/gists', [
                'description' => 'Taskletto Sync',
                'public'      => false,
                'files'       => [self::GIST_FILENAME => ['content' => $payload]],
            ]);
        }
        Log::info("[GistSync][PUSH] HTTP status={$response->status()}");
        if ($response->successful()) {
            $newGistId = $response->json('id');
            AppSetting::set('gist_id', $newGistId);
            $this->recordSyncResult('ok');
            Log::info("[GistSync][PUSH] Sucesso. gist_id={$newGistId}");
            return ['ok' => true, 'message' => 'Dados enviados ao Gist com sucesso.', 'gist_id' => $newGistId];
        }
        $error = $response->json('message') ?? 'Erro desconhecido.';
        $this->recordSyncResult('error', $error);
        Log::error("[GistSync][PUSH] Falhou: {$error} (HTTP {$response->status()})");
        return ['ok' => false, 'message' => "Erro ao enviar: {$error}", 'gist_id' => $gistId];
    }

    public function pull(): array
    {
        Log::info('[GistSync][PULL] ========== INICIANDO PULL ==========');

        if (!self::isConfigured()) {
            Log::warning('[GistSync][PULL] Token nao configurado.');
            return ['ok' => false, 'message' => 'Token nao configurado.', 'stats' => []];
        }

        $gistId = self::gistId();
        Log::info("[GistSync][PULL] gist_id={$gistId}");

        if (!$gistId) {
            Log::warning('[GistSync][PULL] Nenhum gist_id salvo.');
            return ['ok' => false, 'message' => 'Nenhum Gist vinculado. Faca um Push primeiro.', 'stats' => []];
        }

        Log::info("[GistSync][PULL] Chamando API GitHub...");
        $response = $this->http()->get(self::API_BASE . "/gists/{$gistId}");
        Log::info("[GistSync][PULL] HTTP status={$response->status()}");

        if (!$response->successful()) {
            $error = $response->json('message') ?? 'Erro ao buscar Gist.';
            $this->recordSyncResult('error', $error);
            Log::error("[GistSync][PULL] Falha na API: {$error}");
            return ['ok' => false, 'message' => $error, 'stats' => []];
        }

        $gistJson  = $response->json();
        $arquivos  = array_keys($gistJson['files'] ?? []);
        Log::info("[GistSync][PULL] Arquivos no Gist: " . implode(', ', $arquivos));

        $fileEntry = $gistJson['files'][self::GIST_FILENAME] ?? null;
        if (!$fileEntry) {
            Log::error('[GistSync][PULL] Arquivo taskletto-sync.json NAO encontrado no Gist.');
            return ['ok' => false, 'message' => 'Arquivo de sync nao encontrado no Gist.', 'stats' => []];
        }

        $truncated     = $fileEntry['truncated'] ?? false;
        $contentLength = strlen($fileEntry['content'] ?? '');
        Log::info("[GistSync][PULL] truncated={$truncated}, content_length={$contentLength}");

        $rawContent = (!empty($fileEntry['content']) && $truncated !== true)
            ? $fileEntry['content']
            : null;

        if (empty($rawContent) && !empty($fileEntry['raw_url'])) {
            Log::info("[GistSync][PULL] Buscando via raw_url...");
            $rawResp = $this->http()->get($fileEntry['raw_url']);
            Log::info("[GistSync][PULL] raw_url HTTP status={$rawResp->status()}");
            if (!$rawResp->successful()) {
                Log::error('[GistSync][PULL] Falha ao buscar raw_url.');
                return ['ok' => false, 'message' => 'Nao foi possivel baixar o conteudo via raw_url.', 'stats' => []];
            }
            $rawContent = $rawResp->body();
        }

        if (empty($rawContent)) {
            Log::error('[GistSync][PULL] rawContent vazio!');
            return ['ok' => false, 'message' => 'Conteudo do arquivo de sync esta vazio.', 'stats' => []];
        }

        Log::info("[GistSync][PULL] rawContent tamanho=" . strlen($rawContent) . " bytes");

        $data      = json_decode($rawContent, true);
        $jsonError = json_last_error();
        Log::info("[GistSync][PULL] JSON decode: error={$jsonError}, tasks=" . count($data['tasks'] ?? []) . ", notes=" . count($data['notes'] ?? []) . ", categories=" . count($data['categories'] ?? []));

        if ($jsonError !== JSON_ERROR_NONE || !isset($data['tasks'])) {
            Log::error("[GistSync][PULL] JSON invalido. json_error={$jsonError}, has_tasks=" . isset($data['tasks']));
            return ['ok' => false, 'message' => 'Arquivo de sync corrompido ou invalido.', 'stats' => []];
        }

        Log::info('[GistSync][PULL] Iniciando merge...');
        try {
            $stats = $this->merge($data);
        } catch (\Throwable $e) {
            Log::error('[GistSync][PULL] EXCEPTION no merge: ' . $e->getMessage());
            Log::error('[GistSync][PULL] Em: ' . $e->getFile() . ':' . $e->getLine());
            Log::error('[GistSync][PULL] Stack: ' . $e->getTraceAsString());
            return ['ok' => false, 'message' => 'Erro interno no merge: ' . $e->getMessage(), 'stats' => []];
        }

        $this->recordSyncResult('ok');
        Log::info('[GistSync][PULL] Merge OK. Stats: ' . json_encode($stats));

        $parts = [];
        if ($stats['tasks_merged'] > 0 || $stats['tasks_deleted'] > 0) {
            $taskMsg = "{$stats['tasks_merged']} task(s) atualizadas";
            if ($stats['tasks_deleted'] > 0) $taskMsg .= ", {$stats['tasks_deleted']} removida(s)";
            $parts[] = $taskMsg;
        }
        if ($stats['notes_merged'] > 0)      $parts[] = "{$stats['notes_merged']} nota(s)";
        if ($stats['categories_merged'] > 0) $parts[] = "{$stats['categories_merged']} categoria(s)";

        $summary = count($parts) > 0
            ? 'Merge concluido: ' . implode(', ', $parts) . '.'
            : 'Tudo ja estava sincronizado.';

        Log::info("[GistSync][PULL] Retornando: ok=true, message={$summary}");
        Log::info('[GistSync][PULL] ========== FIM DO PULL ==========');

        return ['ok' => true, 'message' => $summary, 'stats' => $stats];
    }

    public function sync(): array
    {
        $push = $this->push();
        if (!$push['ok']) return $push;
        return $this->pull();
    }

    private function merge(array $data): array
    {
        $stats = ['tasks_merged' => 0, 'tasks_deleted' => 0, 'notes_merged' => 0, 'categories_merged' => 0];

        DB::transaction(function () use ($data, &$stats) {

            // ── 1. Categories ─────────────────────────────────────────────────
            Log::info('[GistSync][MERGE] --- CATEGORIAS (' . count($data['categories'] ?? []) . ') ---');
            $categoryIdMap = [];
            foreach ($data['categories'] ?? [] as $rc) {
                $local         = Category::where('name', $rc['name'])->first();
                $remoteUpdated = Carbon::parse($rc['updated_at'] ?? $rc['created_at'] ?? now());
                if ($local) {
                    $categoryIdMap[$rc['id']] = $local->id;
                    if ($remoteUpdated->gt($local->updated_at)) {
                        $local->update(['color' => $rc['color'] ?? $local->color, 'icon' => $rc['icon'] ?? $local->icon, 'description' => $rc['description'] ?? $local->description]);
                        $stats['categories_merged']++;
                        Log::info("[GistSync][MERGE] Categoria '{$rc['name']}': ATUALIZADA");
                    } else {
                        Log::info("[GistSync][MERGE] Categoria '{$rc['name']}': sem alteracao");
                    }
                } else {
                    $new = Category::create(['name' => $rc['name'], 'color' => $rc['color'] ?? '#ff914d', 'icon' => $rc['icon'] ?? null, 'description' => $rc['description'] ?? null]);
                    $categoryIdMap[$rc['id']] = $new->id;
                    $stats['categories_merged']++;
                    Log::info("[GistSync][MERGE] Categoria '{$rc['name']}': CRIADA id={$new->id}");
                }
            }

            // ── 2. Tasks ──────────────────────────────────────────────────────
            Log::info('[GistSync][MERGE] --- TASKS (' . count($data['tasks'] ?? []) . ') ---');
            $taskIdMap = [];

            foreach ($data['tasks'] ?? [] as $rt) {
                $fingerprint   = $rt['title'];
                $remoteCreated = Carbon::parse($rt['created_at'] ?? now());
                $remoteUpdated = Carbon::parse($rt['updated_at'] ?? $rt['created_at'] ?? now());
                $remoteDeleted = $rt['deleted_at'] ?? null;

                Log::info("[GistSync][MERGE] Task '{$fingerprint}': created={$remoteCreated} updated={$remoteUpdated} deleted=" . ($remoteDeleted ?? 'null'));

                $local = Task::where('title', $fingerprint)
                    ->whereBetween('created_at', [$remoteCreated->copy()->subSeconds(5), $remoteCreated->copy()->addSeconds(5)])
                    ->withTrashed()
                    ->first();

                Log::info("[GistSync][MERGE] Task '{$fingerprint}': local=" . ($local ? "id={$local->id} deleted_at={$local->deleted_at} updated_at={$local->updated_at}" : 'NAO ENCONTRADA'));

                $resolvedCategoryId = isset($rt['category_id']) ? ($categoryIdMap[$rt['category_id']] ?? null) : null;

                if ($local) {
                    $taskIdMap[$rt['id']] = $local->id;
                    $remoteIsDeleted = !empty($remoteDeleted);
                    $localIsDeleted  = !is_null($local->deleted_at);
                    $localDeletedAt  = $localIsDeleted ? Carbon::parse($local->deleted_at) : null;

                    Log::info("[GistSync][MERGE] Task '{$fingerprint}': remoteIsDeleted={$remoteIsDeleted} localIsDeleted={$localIsDeleted}");

                    // Caso 1: remoto existe, local deletado
                    if (!$remoteIsDeleted && $localIsDeleted) {
                        if ($localDeletedAt && $localDeletedAt->gte($remoteUpdated)) {
                            Log::info("[GistSync][MERGE] Task '{$fingerprint}': deleção local mais recente, mantendo deletada");
                            continue;
                        }
                        Log::info("[GistSync][MERGE] Task '{$fingerprint}': RESTAURANDO");
                        Task::withoutObservers(fn() => $local->restore());
                        $local->refresh();
                    }

                    // Caso 2: remoto deletado, local existe
                    if ($remoteIsDeleted && !$localIsDeleted) {
                        if ($remoteUpdated->gte($local->updated_at)) {
                            Log::info("[GistSync][MERGE] Task '{$fingerprint}': DELETANDO localmente");
                            Task::withoutObservers(fn() => $local->delete());
                            $stats['tasks_deleted']++;
                        } else {
                            Log::info("[GistSync][MERGE] Task '{$fingerprint}': remoto deletado mas local mais recente, mantendo");
                        }
                        continue;
                    }

                    // Caso 3: atualizar se remoto mais recente
                    if ($remoteUpdated->gt($local->updated_at)) {
                        Log::info("[GistSync][MERGE] Task '{$fingerprint}': ATUALIZANDO");
                        Task::withoutObservers(fn() => $local->update([
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
                    } else {
                        Log::info("[GistSync][MERGE] Task '{$fingerprint}': local ja e mais recente, sem alteracao");
                    }

                } else {
                    if (!empty($remoteDeleted)) {
                        Log::info("[GistSync][MERGE] Task '{$fingerprint}': deletada no remoto e nao existe local, ignorando");
                        continue;
                    }
                    Log::info("[GistSync][MERGE] Task '{$fingerprint}': CRIANDO...");
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
                    Log::info("[GistSync][MERGE] Task '{$fingerprint}': CRIADA id={$new->id}");

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
            Log::info('[GistSync][MERGE] --- NOTAS (' . count($data['notes'] ?? []) . ') ---');

            foreach ($data['notes'] ?? [] as $rn) {
                $remoteCreated     = Carbon::parse($rn['created_at'] ?? now());
                $remoteUpdated     = Carbon::parse($rn['updated_at'] ?? $rn['created_at'] ?? now());
                $noteTitle         = $rn['title'] ?? '(sem titulo)';
                $remoteNoteDeleted = !empty($rn['deleted_at']);

                $local = Note::where('title', $rn['title'])
                    ->whereBetween('created_at', [$remoteCreated->copy()->subSeconds(5), $remoteCreated->copy()->addSeconds(5)])
                    ->withTrashed()
                    ->first();

                Log::info("[GistSync][MERGE] Nota '{$noteTitle}': local=" . ($local ? "id={$local->id}" : 'NAO ENCONTRADA') . " remoteDeleted={$remoteNoteDeleted}");

                if ($local) {
                    $localNoteDeleted = !is_null($local->deleted_at);

                    if (!$remoteNoteDeleted && $localNoteDeleted) {
                        $local->restore();
                        $local->refresh();
                        Log::info("[GistSync][MERGE] Nota '{$noteTitle}': RESTAURADA");
                    }

                    if ($remoteUpdated->gt($local->updated_at) || (!$remoteNoteDeleted && $local->trashed())) {
                        $local->update(['title' => $rn['title'] ?? $local->title, 'content' => $rn['content'] ?? $local->content, 'color' => $rn['color'] ?? $local->color, 'pinned' => $rn['pinned'] ?? $local->pinned, 'tags' => $rn['tags'] ?? $local->tags]);
                        $stats['notes_merged']++;
                        Log::info("[GistSync][MERGE] Nota '{$noteTitle}': ATUALIZADA");
                    } else {
                        Log::info("[GistSync][MERGE] Nota '{$noteTitle}': sem alteracao");
                    }
                } else {
                    if ($remoteNoteDeleted) {
                        Log::info("[GistSync][MERGE] Nota '{$noteTitle}': deletada no remoto, nao recriando");
                        continue;
                    }
                    Note::create(['title' => $rn['title'] ?? null, 'content' => $rn['content'] ?? null, 'color' => $rn['color'] ?? '#ff914d', 'pinned' => $rn['pinned'] ?? false, 'tags' => $rn['tags'] ?? null, 'created_at' => $remoteCreated, 'updated_at' => $remoteUpdated]);
                    $stats['notes_merged']++;
                    Log::info("[GistSync][MERGE] Nota '{$noteTitle}': CRIADA");
                }
            }

            Log::info('[GistSync][MERGE] Transaction concluida. Stats: ' . json_encode($stats));
        });

        return $stats;
    }

    private function buildPayload(): array
    {
        return [
            'meta'       => ['version' => self::EXPORT_VERSION, 'synced_at' => now()->toIso8601String(), 'app' => 'Taskletto'],
            'categories' => Category::orderBy('id')->get()->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'color' => $c->color, 'icon' => $c->icon, 'description' => $c->description, 'created_at' => $c->created_at?->toIso8601String(), 'updated_at' => $c->updated_at?->toIso8601String()])->values(),
            'tasks'      => Task::with(['comments', 'timeLogs'])->withTrashed()->orderBy('id')->get()->map(fn($t) => ['id' => $t->id, 'title' => $t->title, 'description' => $t->description, 'status' => $t->status->value, 'priority' => $t->priority->value, 'category_id' => $t->category_id, 'due_date' => $t->due_date?->toDateString(), 'completed_at' => $t->completed_at?->toIso8601String(), 'recurrence' => $t->recurrence->value, 'recurrence_ends_at' => $t->recurrence_ends_at?->toDateString(), 'estimated_minutes' => $t->estimated_minutes, 'tracked_seconds' => $t->tracked_seconds, 'sort_order' => $t->sort_order, 'created_at' => $t->created_at?->toIso8601String(), 'updated_at' => $t->updated_at?->toIso8601String(), 'deleted_at' => $t->deleted_at?->toIso8601String(), 'comments' => $t->comments->map(fn($c) => ['body' => $c->body, 'created_at' => $c->created_at?->toIso8601String(), 'updated_at' => $c->updated_at?->toIso8601String()])->values()])->values(),
            'notes'      => Note::withTrashed()->orderBy('id')->get()->map(fn($n) => ['id' => $n->id, 'title' => $n->title, 'content' => $n->content, 'color' => $n->color, 'pinned' => $n->pinned, 'tags' => $n->tags, 'created_at' => $n->created_at?->toIso8601String(), 'updated_at' => $n->updated_at?->toIso8601String(), 'deleted_at' => $n->deleted_at?->toIso8601String()])->values(),
        ];
    }

    private function recordSyncResult(string $status, ?string $error = null): void
    {
        AppSetting::set('gist_last_sync_at', now()->toIso8601String());
        AppSetting::set('gist_last_sync_status', $status);
        if ($error) AppSetting::set('gist_last_sync_error', $error);
    }
}