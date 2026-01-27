<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migrate data from network_users table to users table.
     * This consolidates customer network credentials into the User model.
     */
    public function up(): void
    {
        // Only run if network_users table exists
        if (!Schema::hasTable('network_users')) {
            Log::info('network_users table does not exist, skipping migration');
            return;
        }

        try {
            DB::beginTransaction();

            // Get all network users
            $networkUsers = DB::table('network_users')->get();

            Log::info("Migrating {$networkUsers->count()} network users to users table");

            foreach ($networkUsers as $networkUser) {
                // Update the corresponding user record
                if ($networkUser->user_id) {
                    DB::table('users')
                        ->where('id', $networkUser->user_id)
                        ->update([
                            'username' => $networkUser->username,
                            // SECURITY NOTE: radius_password stores plain text for RADIUS authentication
                            // This is required by RADIUS protocol (Cleartext-Password attribute)
                            // Ensure the database has appropriate access controls
                            // Consider using database encryption at rest for additional security
                            'radius_password' => $networkUser->password,
                            'service_type' => $networkUser->service_type,
                            'connection_type' => $networkUser->connection_type ?? null,
                            'billing_type' => $networkUser->billing_type ?? null,
                            'device_type' => $networkUser->device_type ?? null,
                            'mac_address' => $networkUser->mac_address ?? null,
                            'ip_address' => $networkUser->ip_address ?? null,
                            'status' => $networkUser->status,
                            'expiry_date' => $networkUser->expiry_date ?? null,
                            'updated_at' => now(),
                        ]);
                }
            }

            DB::commit();
            Log::info('Network users data migration completed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Network users data migration failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     * 
     * Note: This does not restore data back to network_users table.
     * If you need to rollback, restore from backup.
     */
    public function down(): void
    {
        Log::warning('Rollback for network_user data migration: Data will remain in users table');
        
        // Optionally clear network fields from users where operator_level = 100
        // Update each customer individually to avoid DB-specific SQL
        $customers = DB::table('users')
            ->where('operator_level', 100)
            ->select('id')
            ->get();
        
        foreach ($customers as $customer) {
            DB::table('users')
                ->where('id', $customer->id)
                ->update([
                    'username' => 'user_' . $customer->id, // Generate temporary username
                    'radius_password' => null,
                    'service_type' => null,
                    'connection_type' => null,
                    'billing_type' => null,
                        'device_type' => null,
                    'mac_address' => null,
                    'ip_address' => null,
                    'status' => 'active',
                    'expiry_date' => null,
                ]);
        }
    }
};
