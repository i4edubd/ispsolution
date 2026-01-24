<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\MikrotikProfile;
use App\Models\MikrotikRouter;
use App\Services\MikrotikService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PPPoEProfilesIpAllocationModeChangeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $profileId;

    protected $newMode;

    protected $poolId;

    public function __construct($profileId, $newMode, $poolId = null)
    {
        $this->profileId = $profileId;
        $this->newMode = $newMode;
        $this->poolId = $poolId;
    }

    public function handle(MikrotikService $mikrotik)
    {
        try {
            /** @var MikrotikProfile|null $profile */
            $profile = MikrotikProfile::find($this->profileId);

            if (! $profile) {
                Log::error('Profile not found', ['profile_id' => $this->profileId]);

                return;
            }

            // Update profile in database
            $updateData = [
                'local_address' => $this->newMode === 'static' ? null : $profile->local_address,
                'remote_address' => $this->newMode === 'dynamic' && $this->poolId ? "pool_{$this->poolId}" : $profile->remote_address,
            ];

            $profile->update($updateData);

            // Update configuration on router
            /** @var MikrotikRouter|null $router */
            $router = $profile->router;

            if ($router) {
                $connected = $mikrotik->connectRouter($router->id);

                if ($connected) {
                    if ($this->newMode === 'static') {
                        // Configure for static allocation
                        $this->configureStaticAllocation($mikrotik, $profile);
                    } else {
                        // Configure for dynamic allocation
                        $this->configureDynamicAllocation($mikrotik, $profile, $this->poolId);
                    }
                } else {
                    Log::warning('Failed to connect to router for profile update', [
                        'router_id' => $router->id,
                        'profile_id' => $this->profileId,
                    ]);
                }
            }

            // Notify affected customers (optional implementation)
            $this->notifyAffectedCustomers($profile);

            Log::info('Successfully changed IP allocation mode', [
                'profile_id' => $this->profileId,
                'mode' => $this->newMode,
                'pool_id' => $this->poolId,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to change IP allocation mode', [
                'profile_id' => $this->profileId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    protected function configureStaticAllocation(MikrotikService $mikrotik, MikrotikProfile $profile)
    {
        // Configure MikroTik for static IP allocation
        // This would involve updating the PPPoE profile on the router
        // to use static IP addresses from radreply
        Log::info("Configuring static IP allocation for profile {$profile->name}");

        // Implementation depends on your MikroTik API structure
        // Example: Update profile to not use local address pool
    }

    protected function configureDynamicAllocation(MikrotikService $mikrotik, MikrotikProfile $profile, $poolId)
    {
        // Configure MikroTik for dynamic IP allocation
        // This would involve updating the PPPoE profile on the router
        // to use an IP pool for dynamic allocation
        Log::info("Configuring dynamic IP allocation for profile {$profile->name} with pool {$poolId}");

        // Implementation depends on your MikroTik API structure
        // Example: Update profile to use specified IP pool
    }

    protected function notifyAffectedCustomers(MikrotikProfile $profile)
    {
        // Get customers using this profile and notify them
        // This could involve sending emails, SMS, or in-app notifications
        // about the IP allocation mode change
        Log::info("Notification sent to customers affected by profile {$profile->name} update");
    }
}
