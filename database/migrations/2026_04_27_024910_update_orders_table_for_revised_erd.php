<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add foreign keys for service and machine (from revised ERD)
            $table->unsignedBigInteger('service_id')->nullable()->after('customer_id');
            $table->unsignedBigInteger('machine_id')->nullable()->after('service_id');

            // Add order lifecycle timestamps
            $table->timestamp('received_at')->nullable()->after('pickup_date');
            $table->timestamp('washing_at')->nullable()->after('received_at');
            $table->timestamp('ready_at')->nullable()->after('washing_at');
            $table->timestamp('claimed_at')->nullable()->after('ready_at');

            // Foreign key constraints
            $table->foreign('service_id')->references('id')->on('services')->nullOnDelete();
            $table->foreign('machine_id')->references('id')->on('machines')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropForeign(['machine_id']);
            $table->dropColumn([
                'service_id', 'machine_id',
                'received_at', 'washing_at', 'ready_at', 'claimed_at',
            ]);
        });
    }
};