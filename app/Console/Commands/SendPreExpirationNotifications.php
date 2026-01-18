<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendPreExpirationNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:pre-expiration {--days=3 : Days before expiry to send notification} {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send pre-expiration notifications for invoices expiring soon';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $days = (int) $this->option('days');

        if (!$this->option('force') && !$this->confirm("Send pre-expiration notifications for invoices expiring in {$days} days?")) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $this->info("Sending pre-expiration notifications for invoices expiring in {$days} days...");

        $count = $notificationService->sendPreExpirationReminders($days);

        $this->info("Sent {$count} pre-expiration notification(s).");

        return Command::SUCCESS;
    }
}
