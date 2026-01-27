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
            // Drop existing foreign key constraint
            $table->dropForeign(['nas_id']);

            // Make nas_id nullable by modifying the column definition
            // Using change() without doctrine/dbal in Laravel 12+
            $table->unsignedBigInteger('nas_id')->nullable()->change();

            // Re-add the foreign key constraint with nullable support
            $table->foreign('nas_id')
                ->references('id')
                ->on('nas')
                ->onDelete('cascade');

            // Add router_id as optional alternative to nas_id
            // Note: Foreign key automatically creates an index, so no explicit index needed
            $table->foreignId('router_id')->nullable()->after('nas_id')
                ->constrained('mikrotik_routers')->nullOnDelete();
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

            // Drop the modified nas_id foreign key
            $table->dropForeign(['nas_id']);

            // Note: Not restoring nas_id to non-nullable to prevent data loss
            // Re-add foreign key constraint
            $table->foreign('nas_id')
                ->references('id')
                ->on('nas')
                ->onDelete('cascade');
        });
    }
};
