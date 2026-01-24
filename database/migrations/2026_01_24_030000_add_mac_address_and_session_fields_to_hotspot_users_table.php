<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotspot_users', function (Blueprint $table) {
            $table->string('mac_address')->nullable()->after('password');
            $table->string('active_session_id')->nullable()->after('mac_address');
            $table->timestamp('last_login_at')->nullable()->after('verified_at');
            
            $table->index('mac_address');
            $table->index('active_session_id');
        });
    }

    public function down(): void
    {
        Schema::table('hotspot_users', function (Blueprint $table) {
            $table->dropIndex(['mac_address']);
            $table->dropIndex(['active_session_id']);
            $table->dropColumn(['mac_address', 'active_session_id', 'last_login_at']);
        });
    }
};
