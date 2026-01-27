<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Makes package_id nullable in operator_package_rates table.
     * This field is a legacy field maintained for backward compatibility.
     * New records use master_package_id instead.
     */
    public function up(): void
    {
        Schema::table('operator_package_rates', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['package_id']);
        });

        Schema::table('operator_package_rates', function (Blueprint $table) {
            // Make package_id nullable
            $table->unsignedBigInteger('package_id')->nullable()->change();
        });

        Schema::table('operator_package_rates', function (Blueprint $table) {
            // Re-add foreign key constraint with nullable support
            $table->foreign('package_id')
                ->references('id')
                ->on('packages')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operator_package_rates', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['package_id']);
        });

        Schema::table('operator_package_rates', function (Blueprint $table) {
            // Revert package_id to non-nullable
            $table->unsignedBigInteger('package_id')->nullable(false)->change();
        });

        Schema::table('operator_package_rates', function (Blueprint $table) {
            // Re-add foreign key constraint
            $table->foreign('package_id')
                ->references('id')
                ->on('packages')
                ->onDelete('cascade');
        });
    }
};
