<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->unique();
            $table->string('order_id');
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['cash', 'gcash', 'maya', 'card'])->default('cash');
            $table->string('reference_number')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->enum('status', ['paid', 'unpaid', 'refunded'])->default('unpaid');
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};