<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPaymentJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Payment $payment
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();

            // Process payment logic
            if ($this->payment->status === 'pending') {
                // TODO: Implement actual payment gateway processing
                
                // Simulate payment processing
                $this->payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);

                // Update related invoice
                /** @var \App\Models\Invoice|null $invoice */
                $invoice = $this->payment->invoice;
                if ($invoice) {
                    $totalPaid = Payment::where('invoice_id', $invoice->id)
                        ->where('status', 'completed')
                        ->sum('amount');

                    if ($totalPaid >= $invoice->total_amount) {
                        $invoice->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);
                    }
                }

                Log::info('Payment processed successfully', [
                    'payment_id' => $this->payment->id,
                    'amount' => $this->payment->amount,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to process payment', [
                'payment_id' => $this->payment->id,
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
        Log::error('ProcessPaymentJob failed permanently', [
            'payment_id' => $this->payment->id,
            'error' => $exception?->getMessage(),
        ]);

        // Mark payment as failed
        $this->payment->update(['status' => 'failed']);
    }
}
