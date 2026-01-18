<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendOverdueNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:overdue {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send overdue notifications for overdue invoices';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        if (! $this->option('force') && ! $this->confirm('Send overdue notifications?')) {
            $this->info('Operation cancelled.');

            return Command::SUCCESS;
        }

        $this->info('Sending overdue notifications...');

        $count = $notificationService->sendOverdueNotifications();

        $this->info("Sent {$count} overdue notification(s).");

        return Command::SUCCESS;
    }
}
