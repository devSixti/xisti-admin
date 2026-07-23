<?php

namespace App\Console\Commands;

use App\Services\LiveExchangeRateSyncService;
use Illuminate\Console\Command;

class SyncLiveExchangeRatesCommand extends Command
{
    protected $signature = 'currency:sync-live-rates {--dry-run : Fetch rates without writing to DB}';

    protected $description = 'Sync world_currency.ratio from live FX API (base COP)';

    public function handle(LiveExchangeRateSyncService $syncService): int
    {
        $dryRun = (bool) $this->option('dry-run');

        try {
            $result = $syncService->sync($dryRun);
        } catch (\Throwable $e) {
            $this->error('Exchange rate sync failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $prefix = $dryRun ? '[dry-run] ' : '';
        $this->info($prefix.'Base: '.$result['base']);
        if ($result['provider_time']) {
            $this->line($prefix.'Provider updated: '.$result['provider_time']);
        }
        $this->info($prefix.'Updated: '.$result['updated'].', skipped: '.$result['skipped']);

        return self::SUCCESS;
    }
}
