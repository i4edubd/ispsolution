<?php

namespace App\Console\Commands;

use App\Services\SubscriptionBillingService;
use Illuminate\Console\Command;

class SuspendOverdueSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'subscription:suspend-overdue';

    /**
     * The console command description.
     */
    protected $description = 'Suspend subscriptions with overdue bills';

    /**
     * Execute the console command.
     */
    public function handle(SubscriptionBillingService $subscriptionBillingService): int
    {
        $this->info('Checking for overdue subscription bills...');

        try {
            $suspended = $subscriptionBillingService->suspendForOverdueBills();

            $this->info("Successfully suspended {$suspended} subscriptions.");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to suspend overdue subscriptions: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
