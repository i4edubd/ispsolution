<?php

namespace App\Console\Commands;

use App\Models\MikrotikRouter;
use App\Services\RouterMigrationService;
use Illuminate\Console\Command;

class MigrateRouterToRadiusCommand extends Command
{
    protected $signature = 'mikrotik:migrate-to-radius {router_id : The ID of the router to migrate} {--force : Skip confirmation prompts} {--no-backup : Skip backup creation} {--test-user= : Username to test authentication}';

    protected $description = 'Migrate a MikroTik router from local PPP authentication to RADIUS';

    protected $migrationService;

    public function __construct(RouterMigrationService $migrationService)
    {
        parent::__construct();
        $this->migrationService = $migrationService;
    }

    public function handle()
    {
        $routerId = $this->argument('router_id');
        $router = MikrotikRouter::find($routerId);

        if (!$router) {
            $this->error("Router with ID {$routerId} not found.");
            return 1;
        }

        $this->info("╔══════════════════════════════════════════════════════════╗");
        $this->info("║   MikroTik Router to RADIUS Migration Tool              ║");
        $this->info("╚══════════════════════════════════════════════════════════╝");
        $this->newLine();
        $this->info("Router: {$router->name} ({$router->host})");
        $this->newLine();

        // Confirmation prompt
        if (!$this->option('force')) {
            if (!$this->confirm('This will migrate authentication from local PPP to RADIUS. Continue?')) {
                $this->info('Migration cancelled.');
                return 0;
            }
        }

        // Step 1: Verify RADIUS server connectivity
        $this->info('Step 1/7: Verifying RADIUS server connectivity...');
        if (!$this->migrationService->verifyRadiusConnectivity($router)) {
            $this->error('✗ RADIUS server is not reachable. Please check configuration.');
            return 1;
        }
        $this->info('✓ RADIUS server is reachable');
        $this->newLine();

        // Step 2: Backup current PPP secrets
        $backupFile = null;
        if (!$this->option('no-backup')) {
            $this->info('Step 2/7: Backing up current PPP secrets...');
            $backupFile = $this->migrationService->backupPppSecrets($router);
            $this->info("✓ Backup saved to: {$backupFile}");
            $this->newLine();
        } else {
            $this->warn('Step 2/7: Skipping backup (--no-backup flag set)');
            $this->newLine();
        }

        // Step 3: Configure RADIUS on router
        $this->info('Step 3/7: Configuring RADIUS authentication...');
        if (!$this->migrationService->configureRadiusAuth($router)) {
            $this->error('✗ Failed to configure RADIUS authentication');
            return 1;
        }
        $this->info('✓ RADIUS authentication configured');
        $this->newLine();

        // Step 4: Test RADIUS authentication
        $testUser = $this->option('test-user');
        if ($testUser) {
            $this->info('Step 4/7: Testing RADIUS authentication...');
            if (!$this->migrationService->testRadiusAuth($router, $testUser)) {
                $this->error("✗ RADIUS authentication test failed for user: {$testUser}");
                $this->warn('Rolling back changes...');
                $this->migrationService->rollback($router);
                return 1;
            }
            $this->info("✓ RADIUS authentication successful for user: {$testUser}");
        } else {
            $this->warn('Step 4/7: Skipping authentication test (no --test-user specified)');
        }
        $this->newLine();

        // Step 5: Disable local PPP secrets
        $this->info('Step 5/7: Disabling local PPP secrets...');
        $secretCount = $this->migrationService->disableLocalSecrets($router);
        $this->info("✓ Disabled {$secretCount} local PPP secrets");
        $this->newLine();

        // Step 6: Force disconnect active sessions
        $this->info('Step 6/7: Disconnecting active PPP sessions...');
        $sessionCount = $this->migrationService->disconnectActiveSessions($router);
        $this->info("✓ Disconnected {$sessionCount} active sessions");
        $this->newLine();

        // Step 7: Final verification
        $this->info('Step 7/7: Verifying migration...');
        $status = $this->migrationService->verifyMigration($router);
        
        if ($status['success']) {
            $this->newLine();
            $this->info('╔══════════════════════════════════════════════════════════╗');
            $this->info('║   ✓ Migration completed successfully!                   ║');
            $this->info('╚══════════════════════════════════════════════════════════╝');
            $this->newLine();
            $this->table(
                ['Metric', 'Value'],
                [
                    ['RADIUS Status', 'Enabled'],
                    ['Local Secrets', 'Disabled'],
                    ['Active Sessions', $status['active_sessions']],
                    ['Backup File', $backupFile ?? 'N/A'],
                ]
            );
            return 0;
        } else {
            $this->error('✗ Migration verification failed: ' . $status['message']);
            $this->warn('Rolling back changes...');
            $this->migrationService->rollback($router);
            return 1;
        }
    }
}
