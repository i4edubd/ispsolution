<?php

namespace App\Console\Commands;

use App\Services\SubscriptionBillingService;
use Illuminate\Console\Command;

class GenerateSubscriptionBills extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'subscription:generate-bills';

    /**
     * The console command description.
     */
    protected $description = 'Generate bills for all active subscriptions';

    /**
     * Execute the console command.
     */
    public function handle(SubscriptionBillingService $subscriptionBillingService): int
    {
        $this->info('Generating subscription bills...');

        try {
            $generated = $subscriptionBillingService->generateBillsForAllSubscriptions();

            $this->info("Successfully generated {$generated} subscription bills.");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to generate subscription bills: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
