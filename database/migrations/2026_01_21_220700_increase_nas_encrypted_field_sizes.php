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
        Schema::table('nas', function (Blueprint $table) {
            // Use TEXT for encrypted fields to avoid truncation (Laravel's encrypted cast creates ~200+ char JSON strings)
            $table->text('secret')->change();
            $table->text('community')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nas', function (Blueprint $table) {
            // Revert to original string type
            $table->string('secret', 100)->change();
            $table->string('community', 100)->nullable()->change();
        });
    }
};
