<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\MikrotikServiceInterface;
use App\Models\MikrotikRouter;
use App\Models\RadiusSession;
use Illuminate\Console\Command;

class MikrotikSyncSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mikrotik:sync-sessions 
                            {--router= : Specific router ID to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync active sessions from MikroTik routers to local database';

    /**
     * Execute the console command.
     */
    public function handle(MikrotikServiceInterface $mikrotikService): int
    {
        $routerId = $this->option('router');

        $this->info("Syncing MikroTik sessions...");

        try {
            if ($routerId) {
                // Sync specific router
                $router = MikrotikRouter::findOrFail($routerId);
                return $this->syncRouterSessions($router, $mikrotikService);
            } else {
                // Sync all active routers
                $routers = MikrotikRouter::where('status', 'active')->get();
                
                if ($routers->isEmpty()) {
                    $this->warn("No active routers found");
                    return Command::SUCCESS;
                }

                $totalSynced = 0;
                $failed = 0;

                foreach ($routers as $router) {
                    try {
                        $sessions = $mikrotikService->getActiveSessions($router->id);
                        
                        if ($sessions === null) {
                            $this->error("✗ Failed to fetch sessions from {$router->name}");
                            $failed++;
                            continue;
                        }

                        // Update local session cache
                        foreach ($sessions as $session) {
                            RadiusSession::updateOrCreate(
                                ['session_id' => $session['id'] ?? $session['name']],
                                [
                                    'username' => $session['name'] ?? $session['username'],
                                    'nas_ip_address' => $router->ip_address,
                                    'framed_ip_address' => $session['address'] ?? null,
                                    'start_time' => $session['uptime'] ? now()->subSeconds((int)$session['uptime']) : null,
                                    'input_octets' => $session['bytes-in'] ?? 0,
                                    'output_octets' => $session['bytes-out'] ?? 0,
                                    'status' => 'active',
                                ]
                            );
                        }

                        $count = count($sessions);
                        $this->info("✓ Synced {$count} sessions from {$router->name}");
                        $totalSynced += $count;
                    } catch (\Exception $e) {
                        $this->error("✗ Error syncing {$router->name}: " . $e->getMessage());
                        $failed++;
                    }
                }

                $this->newLine();
                $this->info("Sync Summary:");
                $this->info("  Total sessions synced: {$totalSynced}");
                if ($failed > 0) {
                    $this->warn("  Failed routers: {$failed}");
                }

                return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->error("Router not found");
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error("Session sync failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function syncRouterSessions(MikrotikRouter $router, MikrotikServiceInterface $mikrotikService): int
    {
        $sessions = $mikrotikService->getActiveSessions($router->id);

        if ($sessions === null) {
            $this->error("Failed to fetch sessions from router");
            return Command::FAILURE;
        }

        foreach ($sessions as $session) {
            RadiusSession::updateOrCreate(
                ['session_id' => $session['id'] ?? $session['name']],
                [
                    'username' => $session['name'] ?? $session['username'],
                    'nas_ip_address' => $router->ip_address,
                    'framed_ip_address' => $session['address'] ?? null,
                    'start_time' => $session['uptime'] ? now()->subSeconds((int)$session['uptime']) : null,
                    'input_octets' => $session['bytes-in'] ?? 0,
                    'output_octets' => $session['bytes-out'] ?? 0,
                    'status' => 'active',
                ]
            );
        }

        $count = count($sessions);
        $this->info("✓ Synced {$count} sessions from '{$router->name}'");

        return Command::SUCCESS;
    }
}
