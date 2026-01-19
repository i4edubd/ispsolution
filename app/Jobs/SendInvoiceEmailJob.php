<?php

namespace App\Jobs;

use App\Models\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInvoiceEmailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Invoice $invoice,
        public string $emailType = 'new' // new, reminder, overdue
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            /** @var \App\Models\User|null $user */
            $user = $this->invoice->user;

            if (! $user || ! filter_var($user->email ?? '', FILTER_VALIDATE_EMAIL)) {
                Log::warning('Cannot send invoice email: User or email not found', [
                    'invoice_id' => $this->invoice->id,
                ]);
                return;
            }

            // Send email based on type
            $mailData = [
                'invoice' => $this->invoice,
                'user' => $user,
                'type' => $this->emailType,
            ];

            // TODO: Implement actual mail sending with Mailable class
            // Mail::to($user->email)->send(new InvoiceMail($mailData));

            Log::info('Invoice email queued successfully', [
                'invoice_id' => $this->invoice->id,
                'email' => $user->email,
                'type' => $this->emailType,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send invoice email', [
                'invoice_id' => $this->invoice->id,
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
        Log::error('SendInvoiceEmailJob failed permanently', [
            'invoice_id' => $this->invoice->id,
            'error' => $exception?->getMessage(),
        ]);
    }
}
