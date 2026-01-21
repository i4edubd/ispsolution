<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ip_pools', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->enum('pool_type', ['public', 'private'])->default('public');
            $table->string('start_ip', 45)->nullable();
            $table->string('end_ip', 45)->nullable();
            $table->string('gateway', 45)->nullable();
            $table->string('dns_servers', 255)->nullable();
            $table->integer('vlan_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('status');
            $table->index('pool_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ip_pools');
    }
};
