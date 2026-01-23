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
        Schema::table('olts', function (Blueprint $table) {
            // Increase username column length to accommodate encrypted data
            // Encrypted strings are typically 200+ characters
            $table->string('username', 500)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('olts', function (Blueprint $table) {
            // Revert username column length to original size
            $table->string('username', 100)->nullable()->change();
        });
    }
};
