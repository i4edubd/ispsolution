<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBulkSmsJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $phoneNumbers,
        public string $message,
        public int $tenantId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Sending bulk SMS', [
                'tenant_id' => $this->tenantId,
                'recipient_count' => count($this->phoneNumbers),
            ]);

            // TODO: Implement actual SMS gateway integration
            // - Connect to SMS gateway
            // - Send messages in batches
            // - Log delivery status

            foreach ($this->phoneNumbers as $phoneNumber) {
                // Simulate SMS sending
                Log::debug('SMS sent', [
                    'phone' => $phoneNumber,
                    'message' => substr($this->message, 0, 50),
                ]);
            }

            Log::info('Bulk SMS sent successfully', [
                'tenant_id' => $this->tenantId,
                'sent_count' => count($this->phoneNumbers),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send bulk SMS', [
                'tenant_id' => $this->tenantId,
                'recipient_count' => count($this->phoneNumbers),
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        Log::error('SendBulkSmsJob failed permanently', [
            'tenant_id' => $this->tenantId,
            'recipient_count' => count($this->phoneNumbers),
            'error' => $exception?->getMessage(),
        ]);
    }
}
