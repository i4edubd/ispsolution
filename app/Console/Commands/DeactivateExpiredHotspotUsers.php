<?php

namespace App\Console\Commands;

use App\Services\HotspotService;
use Illuminate\Console\Command;

class DeactivateExpiredHotspotUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotspot:deactivate-expired {--tenant= : Specific tenant ID} {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate expired hotspot users';

    /**
     * Execute the console command.
     */
    public function handle(HotspotService $hotspotService): int
    {
        if (! $this->option('force') && ! $this->confirm('Do you want to deactivate expired hotspot users?')) {
            $this->info('Operation cancelled.');

            return Command::SUCCESS;
        }

        $this->info('Deactivating expired hotspot users...');

        $tenantId = $this->option('tenant');

        if ($tenantId) {
            $count = $hotspotService->deactivateExpiredUsers((int) $tenantId);
            $this->info("Deactivated {$count} expired hotspot user(s) for tenant {$tenantId}.");
        } else {
            // Process all tenants
            $tenants = \App\Models\Tenant::pluck('id');
            $totalCount = 0;

            foreach ($tenants as $tid) {
                $count = $hotspotService->deactivateExpiredUsers($tid);
                $totalCount += $count;
            }

            $this->info("Deactivated {$totalCount} expired hotspot user(s) across all tenants.");
        }

        return Command::SUCCESS;
    }
}
