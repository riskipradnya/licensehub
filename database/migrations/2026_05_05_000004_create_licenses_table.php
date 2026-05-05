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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->enum('type', ['subscription', 'perpetual'])->default('subscription');
            $table->string('serial_key')->nullable();
            $table->date('start_date');
            $table->date('expiry_date')->nullable();
            $table->integer('seats')->nullable();
            $table->decimal('cost', 15, 2);
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly', 'one_time'])->default('yearly');
            $table->enum('status', ['active', 'expiring', 'expired', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
