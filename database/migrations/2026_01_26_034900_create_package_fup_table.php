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
        Schema::create('package_fup', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
            $table->enum('type', ['data', 'time', 'both'])->default('data');
            $table->bigInteger('data_limit_bytes')->nullable()->comment('Data limit in bytes');
            $table->integer('time_limit_minutes')->nullable()->comment('Time limit in minutes');
            $table->string('reduced_speed')->nullable()->comment('Speed after FUP reached, e.g., "1M/512k"');
            $table->enum('reset_period', ['daily', 'weekly', 'monthly'])->default('monthly');
            $table->boolean('notify_customer')->default(true);
            $table->integer('notify_at_percent')->default(80)->comment('Notify when usage reaches this percentage');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('package_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_fup');
    }
};
