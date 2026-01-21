<?php

namespace App\Jobs;

use App\Models\MikrotikRouter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncMikrotikSessionJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 90;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $routerId,
        public ?string $username = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $router = MikrotikRouter::findOrFail($this->routerId);

            Log::info('Syncing MikroTik session', [
                'router_id' => $this->routerId,
                'router_name' => $router->name,
                'username' => $this->username,
            ]);

            $mikrotikService = app(\App\Services\MikrotikService::class);
            
            // Sync PPPoE sessions from the router
            if ($this->username) {
                // Sync specific user session
                $session = $mikrotikService->getPppoeUserSession($router, $this->username);
                if ($session) {
                    $mikrotikService->syncSessionToDatabase($router, $session);
                    Log::debug('Synced specific user session', [
                        'username' => $this->username,
                        'session' => $session,
                    ]);
                }
            } else {
                // Sync all active sessions
                $sessions = $mikrotikService->getActivePppoeSessions($router);
                $syncedCount = 0;
                
                foreach ($sessions as $session) {
                    $mikrotikService->syncSessionToDatabase($router, $session);
                    $syncedCount++;
                }
                
                Log::debug('Synced all sessions', [
                    'synced_count' => $syncedCount,
                ]);
            }

            // Update router last sync time
            $router->update(['last_sync_at' => now()]);

            Log::info('MikroTik session synced successfully', [
                'router_id' => $this->routerId,
                'username' => $this->username,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to sync MikroTik session', [
                'router_id' => $this->routerId,
                'username' => $this->username,
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
        Log::error('SyncMikrotikSessionJob failed permanently', [
            'router_id' => $this->routerId,
            'username' => $this->username,
            'error' => $exception?->getMessage(),
        ]);
    }
}
