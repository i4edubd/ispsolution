<?php

namespace App\Services;

use App\Models\MikrotikRouter;
use App\Models\Radius\Radcheck;
use App\Services\MikrotikService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class RouterMigrationService
{
    protected $mikrotik;
    
    public function __construct(MikrotikService $mikrotik)
    {
        $this->mikrotik = $mikrotik;
    }
    
    public function verifyRadiusConnectivity(MikrotikRouter $router): bool
    {
        try {
            // Check if RADIUS server is configured by querying router
            $response = Http::timeout(config('services.mikrotik.timeout', 30))
                ->get("http://{$router->ip_address}:{$router->api_port}/api/radius/print");
            
            if (!$response->successful()) {
                return false;
            }
            
            $radiusServers = $response->json();
            
            foreach ($radiusServers as $server) {
                if (isset($server['address']) && $server['address'] === config('radius.server')) {
                    // Test connectivity by pinging RADIUS server
                    $pingResponse = Http::timeout(config('services.mikrotik.timeout', 30))
                        ->post("http://{$router->ip_address}:{$router->api_port}/api/ping", [
                            'address' => config('radius.server'),
                            'count' => 3
                        ]);
                    
                    if ($pingResponse->successful()) {
                        $pingResult = $pingResponse->json();
                        return isset($pingResult['received']) && $pingResult['received'] > 0;
                    }
                }
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error("Failed to verify RADIUS connectivity: " . $e->getMessage());
            return false;
        }
    }
    
    public function backupPppSecrets(MikrotikRouter $router): string
    {
        // Get all PPP secrets
        $response = Http::timeout(config('services.mikrotik.timeout', 30))
            ->get("http://{$router->ip_address}:{$router->api_port}/api/ppp/secret/print");
        
        $secrets = $response->successful() ? $response->json() : [];
        
        // Create backup file
        $timestamp = now()->format('Y-m-d_His');
        $filename = "router_{$router->id}_ppp_secrets_{$timestamp}.json";
        $path = "backups/router-migrations/{$filename}";
        
        Storage::put($path, json_encode($secrets, JSON_PRETTY_PRINT));
        
        // Also store rollback info in cache
        cache()->put("router:{$router->id}:migration:backup", $path, now()->addDays(7));
        
        return $path;
    }
    
    public function configureRadiusAuth(MikrotikRouter $router): bool
    {
        try {
            // Check if RADIUS is already configured
            $response = Http::timeout(config('services.mikrotik.timeout', 30))
                ->get("http://{$router->ip_address}:{$router->api_port}/api/radius/print");
            
            if (!$response->successful()) {
                return false;
            }
            
            $radiusServers = $response->json();
            $radiusExists = false;
            
            foreach ($radiusServers as $server) {
                if (isset($server['address']) && $server['address'] === config('radius.server')) {
                    $radiusExists = true;
                    break;
                }
            }
            
            // Add RADIUS server if not exists
            if (!$radiusExists) {
                $addResponse = Http::timeout(config('services.mikrotik.timeout', 30))
                    ->post("http://{$router->ip_address}:{$router->api_port}/api/radius/add", [
                        'address' => config('radius.server'),
                        'secret' => config('radius.secret'),
                        'service' => 'ppp',
                        'authentication-port' => config('radius.auth_port', 1812),
                        'accounting-port' => config('radius.acct_port', 1813),
                    ]);
                
                if (!$addResponse->successful()) {
                    return false;
                }
            }
            
            // Enable RADIUS for PPP
            $aaaResponse = Http::timeout(config('services.mikrotik.timeout', 30))
                ->post("http://{$router->ip_address}:{$router->api_port}/api/ppp/aaa/set", [
                    'use-radius' => 'yes',
                ]);
            
            return $aaaResponse->successful();
        } catch (\Exception $e) {
            Log::error("Failed to configure RADIUS auth: " . $e->getMessage());
            return false;
        }
    }
    
    public function testRadiusAuth(MikrotikRouter $router, string $username): bool
    {
        try {
            // Check if the user exists in radcheck
            $user = Radcheck::where('username', $username)->first();
            
            return $user !== null;
        } catch (\Exception $e) {
            Log::error("Failed to test RADIUS auth: " . $e->getMessage());
            return false;
        }
    }
    
    public function disableLocalSecrets(MikrotikRouter $router): int
    {
        $response = Http::timeout(config('services.mikrotik.timeout', 30))
            ->get("http://{$router->ip_address}:{$router->api_port}/api/ppp/secret/print");
        
        if (!$response->successful()) {
            return 0;
        }
        
        $secrets = $response->json();
        $count = 0;
        
        foreach ($secrets as $secret) {
            if (isset($secret['.id'])) {
                $disableResponse = Http::timeout(config('services.mikrotik.timeout', 30))
                    ->post("http://{$router->ip_address}:{$router->api_port}/api/ppp/secret/disable", [
                        '.id' => $secret['.id']
                    ]);
                
                if ($disableResponse->successful()) {
                    $count++;
                }
            }
        }
        
        return $count;
    }
    
    public function disconnectActiveSessions(MikrotikRouter $router): int
    {
        $response = Http::timeout(config('services.mikrotik.timeout', 30))
            ->get("http://{$router->ip_address}:{$router->api_port}/api/ppp/active/print");
        
        if (!$response->successful()) {
            return 0;
        }
        
        $activeSessions = $response->json();
        $count = 0;
        
        foreach ($activeSessions as $session) {
            if (isset($session['.id'])) {
                $removeResponse = Http::timeout(config('services.mikrotik.timeout', 30))
                    ->post("http://{$router->ip_address}:{$router->api_port}/api/ppp/active/remove", [
                        '.id' => $session['.id']
                    ]);
                
                if ($removeResponse->successful()) {
                    $count++;
                }
            }
        }
        
        return $count;
    }
    
    public function verifyMigration(MikrotikRouter $router): array
    {
        try {
            // Check RADIUS is enabled
            $response = Http::timeout(config('services.mikrotik.timeout', 30))
                ->get("http://{$router->ip_address}:{$router->api_port}/api/ppp/aaa/print");
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Failed to verify PPP AAA settings'
                ];
            }
            
            $aaa = $response->json();
            
            if (!isset($aaa[0]['use-radius']) || $aaa[0]['use-radius'] !== 'true') {
                return [
                    'success' => false,
                    'message' => 'RADIUS is not enabled for PPP'
                ];
            }
            
            // Count active sessions
            $sessionsResponse = Http::timeout(config('services.mikrotik.timeout', 30))
                ->get("http://{$router->ip_address}:{$router->api_port}/api/ppp/active/print");
            
            $activeSessions = $sessionsResponse->successful() ? $sessionsResponse->json() : [];
            
            return [
                'success' => true,
                'active_sessions' => count($activeSessions),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    public function rollback(MikrotikRouter $router): bool
    {
        try {
            // Get backup path
            $backupPath = cache()->get("router:{$router->id}:migration:backup");
            
            if (!$backupPath) {
                throw new \Exception("No backup found for router {$router->id}");
            }
            
            // Restore PPP secrets from backup
            $secrets = json_decode(Storage::get($backupPath), true);
            
            foreach ($secrets as $secret) {
                if (isset($secret['.id'])) {
                    // Re-enable disabled secrets
                    Http::timeout(config('services.mikrotik.timeout', 30))
                        ->post("http://{$router->ip_address}:{$router->api_port}/api/ppp/secret/enable", [
                            '.id' => $secret['.id']
                        ]);
                }
            }
            
            // Disable RADIUS
            $response = Http::timeout(config('services.mikrotik.timeout', 30))
                ->post("http://{$router->ip_address}:{$router->api_port}/api/ppp/aaa/set", [
                    'use-radius' => 'no',
                ]);
            
            Log::info("Successfully rolled back router {$router->id} migration");
            return $response->successful();
            
        } catch (\Exception $e) {
            Log::error("Failed to rollback router migration: " . $e->getMessage());
            return false;
        }
    }
}
