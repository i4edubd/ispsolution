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
        Schema::create('sales_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('type');
            $table->string('subject');
            $table->text('comment');
            $table->timestamp('contact_date');
            $table->text('next_action')->nullable();
            $table->timestamp('next_action_date')->nullable();
            $table->string('attachment_path')->nullable();
            $table->boolean('is_private')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('lead_id');
            $table->index('customer_id');
            $table->index('type');
            $table->index(['tenant_id', 'user_id']);
            $table->index(['tenant_id', 'contact_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_comments');
    }
};
