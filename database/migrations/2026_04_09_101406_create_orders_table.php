<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('machine_id')->nullable();
            $table->string('service');
            $table->string('laundry_type');
            $table->string('laundry_type_other')->nullable();
            $table->decimal('weight', 6, 2);
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('pickup_date');
            $table->timestamp('received_at')->nullable();
            $table->timestamp('washing_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->enum('status', [
                'pending', 'washing', 'drying', 'ready', 'claimed', 'completed', 'cancelled'
            ])->nullable()->default('pending');
            $table->unsignedBigInteger('cashier_id')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('cashier_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};