<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mikrotik_routers', function (Blueprint $table) {
            // Add NAS relationship
            if (!Schema::hasColumn('mikrotik_routers', 'nas_id')) {
                $table->foreignId('nas_id')->nullable()->after('tenant_id')
                    ->constrained('nas')->nullOnDelete();
            }

            // Add RADIUS secret (encrypted)
            if (!Schema::hasColumn('mikrotik_routers', 'radius_secret')) {
                $table->string('radius_secret', 255)->nullable()->after('password');
            }

            // Add public IP address
            if (!Schema::hasColumn('mikrotik_routers', 'public_ip')) {
                $table->string('public_ip', 45)->nullable()->after('ip_address');
            }

            // Add primary authentication mode
            if (!Schema::hasColumn('mikrotik_routers', 'primary_auth')) {
                $table->enum('primary_auth', ['radius', 'router', 'hybrid'])
                    ->default('hybrid')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mikrotik_routers', function (Blueprint $table) {
            if (Schema::hasColumn('mikrotik_routers', 'nas_id')) {
                $table->dropForeign(['nas_id']);
                $table->dropColumn('nas_id');
            }

            if (Schema::hasColumn('mikrotik_routers', 'radius_secret')) {
                $table->dropColumn('radius_secret');
            }

            if (Schema::hasColumn('mikrotik_routers', 'public_ip')) {
                $table->dropColumn('public_ip');
            }

            if (Schema::hasColumn('mikrotik_routers', 'primary_auth')) {
                $table->dropColumn('primary_auth');
            }
        });
    }
};
