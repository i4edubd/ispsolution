<?php

namespace App\Services;

use App\Mail\InvoiceExpiringSoon;
use App\Mail\InvoiceGenerated;
use App\Mail\InvoiceOverdue;
use App\Mail\PaymentReceived;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send invoice generated notification
     */
    public function sendInvoiceGenerated(Invoice $invoice): bool
    {
        try {
            if ($invoice->user && $invoice->user->email) {
                Mail::to($invoice->user->email)
                    ->send(new InvoiceGenerated($invoice));
                
                Log::info('Invoice generated email sent', [
                    'invoice_id' => $invoice->id,
                    'user_id' => $invoice->user_id,
                ]);
                
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send invoice generated email', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }
        
        return false;
    }

    /**
     * Send payment received notification
     */
    public function sendPaymentReceived(Payment $payment): bool
    {
        try {
            if ($payment->user && $payment->user->email) {
                Mail::to($payment->user->email)
                    ->send(new PaymentReceived($payment));
                
                Log::info('Payment received email sent', [
                    'payment_id' => $payment->id,
                    'user_id' => $payment->user_id,
                ]);
                
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send payment received email', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
        
        return false;
    }

    /**
     * Send invoice overdue notification
     */
    public function sendInvoiceOverdue(Invoice $invoice): bool
    {
        try {
            if ($invoice->user && $invoice->user->email) {
                Mail::to($invoice->user->email)
                    ->send(new InvoiceOverdue($invoice));
                
                Log::info('Invoice overdue email sent', [
                    'invoice_id' => $invoice->id,
                    'user_id' => $invoice->user_id,
                ]);
                
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send invoice overdue email', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }
        
        return false;
    }

    /**
     * Send invoice expiring soon notification
     */
    public function sendInvoiceExpiringSoon(Invoice $invoice, int $daysUntilExpiry): bool
    {
        try {
            if ($invoice->user && $invoice->user->email) {
                Mail::to($invoice->user->email)
                    ->send(new InvoiceExpiringSoon($invoice, $daysUntilExpiry));
                
                Log::info('Invoice expiring soon email sent', [
                    'invoice_id' => $invoice->id,
                    'user_id' => $invoice->user_id,
                    'days_until_expiry' => $daysUntilExpiry,
                ]);
                
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send invoice expiring soon email', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }
        
        return false;
    }

    /**
     * Send pre-expiration reminders for invoices expiring in N days
     */
    public function sendPreExpirationReminders(int $daysBeforeExpiry = 3): int
    {
        $targetDate = now()->addDays($daysBeforeExpiry)->format('Y-m-d');
        
        $expiringInvoices = Invoice::whereDate('due_date', $targetDate)
            ->where('status', 'pending')
            ->with(['user', 'package'])
            ->get();

        $count = 0;
        foreach ($expiringInvoices as $invoice) {
            if ($this->sendInvoiceExpiringSoon($invoice, $daysBeforeExpiry)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Send overdue notifications for all overdue invoices
     */
    public function sendOverdueNotifications(): int
    {
        $overdueInvoices = Invoice::where('status', 'overdue')
            ->whereDate('due_date', '<', now())
            ->with(['user', 'package'])
            ->get();

        $count = 0;
        foreach ($overdueInvoices as $invoice) {
            // Check if notification was sent recently (avoid spam)
            $lastNotificationKey = "overdue_notification_{$invoice->id}";
            $lastSent = cache($lastNotificationKey);
            
            if (!$lastSent || $lastSent->diffInDays(now()) >= 7) {
                if ($this->sendInvoiceOverdue($invoice)) {
                    cache([$lastNotificationKey => now()], now()->addDays(7));
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Queue notifications (for better performance)
     */
    public function queueInvoiceGenerated(Invoice $invoice): void
    {
        if ($invoice->user && $invoice->user->email) {
            Mail::to($invoice->user->email)
                ->queue(new InvoiceGenerated($invoice));
        }
    }

    /**
     * Queue payment notification
     */
    public function queuePaymentReceived(Payment $payment): void
    {
        if ($payment->user && $payment->user->email) {
            Mail::to($payment->user->email)
                ->queue(new PaymentReceived($payment));
        }
    }
}
