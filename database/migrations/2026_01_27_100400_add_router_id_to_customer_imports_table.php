<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Note: Application-level validation ensures at least one of nas_id or router_id is present.
     * See CustomerImportController::store() for validation logic.
     */
    public function up(): void
    {
        Schema::table('customer_imports', function (Blueprint $table) {
            // Make nas_id nullable since we might use router_id instead
            $table->foreignId('nas_id')->nullable()->change();

            // Add router_id as optional alternative to nas_id
            $table->foreignId('router_id')->nullable()->after('nas_id')
                ->constrained('mikrotik_routers')->nullOnDelete();

            $table->index('router_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_imports', function (Blueprint $table) {
            $table->dropForeign(['router_id']);
            $table->dropColumn('router_id');

            // Note: Not restoring nas_id to non-nullable to prevent data loss
            // It remains nullable for backward compatibility
        });
    }
};
