<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PackageFup;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class FupService
{
    /**
     * Check if user has exceeded FUP limits.
     */
    public function checkFupStatus(User $user): array
    {
        $package = $user->package;
        
        if (!$package || !$package->fup) {
            return [
                'has_fup' => false,
                'exceeded' => false,
                'should_notify' => false,
            ];
        }

        $fup = $package->fup;
        $usage = $this->getUserUsage($user);

        $exceeded = $fup->isExceeded($usage['bytes'], $usage['minutes']);
        $shouldNotify = $fup->shouldNotify($usage['bytes'], $usage['minutes']);

        return [
            'has_fup' => true,
            'exceeded' => $exceeded,
            'should_notify' => $shouldNotify,
            'fup' => $fup,
            'usage' => $usage,
            'data_percent' => $fup->getDataUsagePercent($usage['bytes']),
            'time_percent' => $fup->getTimeUsagePercent($usage['minutes']),
        ];
    }

    /**
     * Get user's current usage.
     */
    protected function getUserUsage(User $user): array
    {
        // This would integrate with actual RADIUS accounting data
        // For now, returning mock data structure
        return [
            'bytes' => 0, // Get from RADIUS acct_input_octets + acct_output_octets
            'minutes' => 0, // Get from RADIUS acct_session_time / 60
        ];
    }

    /**
     * Enforce FUP by reducing speed on router.
     */
    public function enforceFup(User $user): bool
    {
        $status = $this->checkFupStatus($user);

        if (!$status['has_fup'] || !$status['exceeded']) {
            return false;
        }

        $fup = $status['fup'];
        
        if (!$fup->reduced_speed) {
            // If no reduced speed specified, just log
            Log::info("FUP exceeded for user {$user->id} but no reduced speed configured");
            return false;
        }

        // Apply reduced speed to router
        // This would integrate with MikroTik API to change speed
        Log::info("Applying FUP speed limit for user {$user->id}: {$fup->reduced_speed}");

        // TODO: Integrate with MikroTik service to apply speed limit
        // Example: $this->mikrotikService->changeCustomerSpeed($user, $fup->reduced_speed);

        return true;
    }

    /**
     * Send notification to customer about FUP.
     */
    public function sendFupNotification(User $user): void
    {
        $status = $this->checkFupStatus($user);

        if (!$status['should_notify']) {
            return;
        }

        // TODO: Implement notification sending
        // Could be SMS, email, or in-app notification
        Log::info("FUP notification should be sent to user {$user->id}");
    }

    /**
     * Reset FUP usage based on reset period.
     */
    public function resetFupUsage(PackageFup $fup): void
    {
        // This would be called by a scheduled job
        // Reset logic depends on the reset_period (daily, weekly, monthly)
        
        Log::info("Resetting FUP usage for package {$fup->package_id}");
        
        // TODO: Implement actual reset logic
        // This might involve:
        // 1. Resetting customer speeds back to normal
        // 2. Clearing usage counters
        // 3. Sending notification that FUP has been reset
    }

    /**
     * Get FUP statistics for a package.
     */
    public function getPackageFupStats(int $packageId): array
    {
        // Get statistics about how many customers are affected by FUP
        return [
            'total_customers' => 0,
            'exceeded_customers' => 0,
            'near_limit_customers' => 0,
        ];
    }
}
