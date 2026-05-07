<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('machine_number')->unique();
            $table->enum('type', ['washer', 'dryer'])->default('washer');
            $table->enum('status', ['available', 'in_use', 'under_maintenance'])->default('available');
            $table->decimal('capacity_kg', 5, 2)->default(7.00); // max kg capacity per machine
            $table->string('current_order_id')->nullable();
            $table->string('maintenance_note')->nullable();
            $table->timestamp('last_maintained_at')->nullable();
            $table->timestamp('maintenance_due_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};