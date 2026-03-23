<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Services\GistSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GistSyncController extends Controller
{
    public function __construct(private GistSyncService $sync) {}

    // ── Save token & optional gist_id ────────────────────────────────────────

    public function saveConfig(Request $request): JsonResponse
    {
        $request->validate([
            'token'   => ['required', 'string', 'min:10', 'max:200'],
            'gist_id' => ['nullable', 'string', 'max:100'],
        ]);

        AppSetting::set('gist_token', trim($request->token));

        if ($request->filled('gist_id')) {
            AppSetting::set('gist_id', trim($request->gist_id));
        }

        return response()->json(['ok' => true, 'message' => 'Configuração salva.']);
    }

    // ── Clear token (disconnect) ──────────────────────────────────────────────

    public function disconnect(): JsonResponse
    {
        AppSetting::set('gist_token', null);
        AppSetting::set('gist_id', null);
        AppSetting::set('gist_last_sync_at', null);
        AppSetting::set('gist_last_sync_status', 'never');

        return response()->json(['ok' => true, 'message' => 'Sync desconectado.']);
    }

    // ── Manual push ───────────────────────────────────────────────────────────

    public function push(): JsonResponse
    {
        $result = $this->sync->push();
        return response()->json($result, $result['ok'] ? 200 : 422);
    }

    // ── Manual pull ───────────────────────────────────────────────────────────

    public function pull(): JsonResponse
    {
        $result = $this->sync->pull();
        return response()->json($result, $result['ok'] ? 200 : 422);
    }

    // ── Status (for the UI polling) ───────────────────────────────────────────

    public function status(): JsonResponse
    {
        return response()->json([
            'configured'    => GistSyncService::isConfigured(),
            'gist_id'       => GistSyncService::gistId(),
            'last_sync_at'  => GistSyncService::lastSyncAt(),
            'last_status'   => GistSyncService::lastSyncStatus(),
            'last_error'    => AppSetting::get('gist_last_sync_error'),
            'interval_min'  => (int) AppSetting::get('gist_sync_interval', 15),
        ]);
    }

    // ── Update auto-sync interval ─────────────────────────────────────────────

    public function setInterval(Request $request): JsonResponse
    {
        $request->validate(['interval' => ['required', 'integer', 'in:5,10,15,30,60']]);
        AppSetting::set('gist_sync_interval', $request->interval);
        return response()->json(['ok' => true]);
    }

    // ── List gists from GitHub account (for picker UI) ────────────────────────

    public function listGists(Request $request): JsonResponse
    {
        $request->validate(['token' => ['required', 'string', 'min:10', 'max:200']]);

        $response = \Illuminate\Support\Facades\Http::withToken(trim($request->token))
            ->withHeaders(['Accept' => 'application/vnd.github+json', 'User-Agent' => 'Taskletto-App'])
            ->timeout(15)
            ->get('https://api.github.com/gists?per_page=50');

        if (!$response->successful()) {
            $msg = $response->status() === 401
                ? 'Token inválido ou sem permissão. Verifique o escopo "gist".'
                : ($response->json('message') ?? 'Erro ao acessar o GitHub.');
            return response()->json(['ok' => false, 'message' => $msg]);
        }

        $gists = collect($response->json())->map(fn($g) => [
            'id'           => $g['id'],
            'description'  => $g['description'] ?: '(sem descrição)',
            'files'        => array_keys($g['files'] ?? []),
            'updated_at'   => $g['updated_at'],
            'is_taskletto' => isset($g['files']['taskletto-sync.json']),
        ])->sortByDesc('is_taskletto')->values();

        return response()->json(['ok' => true, 'gists' => $gists]);
    }
}