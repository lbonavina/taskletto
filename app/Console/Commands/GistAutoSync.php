<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use App\Services\GistSyncService;
use Illuminate\Console\Command;

class GistAutoSync extends Command
{
    protected $signature   = 'gist:auto-sync';
    protected $description = 'Sincroniza automaticamente com o GitHub Gist se configurado e o intervalo tiver passado.';

    public function handle(GistSyncService $sync): int
    {
        // Check if feature is configured
        if (!GistSyncService::isConfigured()) {
            $this->line('Gist sync não configurado — pulando.');
            return self::SUCCESS;
        }

        // Check interval
        $intervalMinutes = (int) AppSetting::get('gist_sync_interval', 15);
        $lastSync        = AppSetting::get('gist_last_sync_at');

        if ($lastSync) {
            $minutesSinceLast = now()->diffInMinutes(\Carbon\Carbon::parse($lastSync));
            if ($minutesSinceLast < $intervalMinutes) {
                $remaining = $intervalMinutes - $minutesSinceLast;
                $this->line("Próximo sync em {$remaining} min — pulando.");
                return self::SUCCESS;
            }
        }

        $this->info('Iniciando sync com GitHub Gist…');

        // Push local → Gist
        $push = $sync->push();
        if (!$push['ok']) {
            $this->error("Push falhou: {$push['message']}");
            return self::FAILURE;
        }
        $this->info("Push OK — Gist ID: {$push['gist_id']}");

        // Pull Gist → local (merge)
        $pull = $sync->pull();
        if (!$pull['ok']) {
            $this->error("Pull falhou: {$pull['message']}");
            return self::FAILURE;
        }
        $this->info("Pull OK — {$pull['message']}");

        return self::SUCCESS;
    }
}
