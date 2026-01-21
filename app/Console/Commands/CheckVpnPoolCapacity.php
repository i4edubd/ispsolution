<?php

namespace App\Console\Commands;

use App\Services\VpnService;
use Illuminate\Console\Command;

class CheckVpnPoolCapacity extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'vpn:check-capacity {--threshold=80}';

    /**
     * The console command description.
     */
    protected $description = 'Check VPN pool capacity and alert if threshold is exceeded';

    /**
     * Execute the console command.
     */
    public function handle(VpnService $vpnService): int
    {
        $this->info('Checking VPN pool capacity...');

        try {
            $threshold = (int) $this->option('threshold');
            $pools = \App\Models\VpnPool::active()->get();

            $criticalPools = 0;

            foreach ($pools as $pool) {
                $capacity = $vpnService->checkPoolCapacity($pool->id, $threshold);

                if ($capacity['is_critical']) {
                    $this->error("CRITICAL: {$capacity['pool_name']} - {$capacity['usage_percentage']}% used");
                    $this->line("  → {$capacity['recommendation']}");
                    $criticalPools++;
                } else {
                    $this->info("{$capacity['pool_name']} - {$capacity['usage_percentage']}% used ({$capacity['available_ips']} IPs available)");
                }
            }

            if ($criticalPools > 0) {
                $this->warn("Found {$criticalPools} critical pools requiring attention.");
            } else {
                $this->info('All VPN pools are operating within acceptable capacity.');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to check VPN pool capacity: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
