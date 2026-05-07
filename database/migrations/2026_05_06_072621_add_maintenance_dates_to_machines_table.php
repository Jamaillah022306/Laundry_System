<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->timestamp('last_maintained_at')->nullable()->after('maintenance_note');
            $table->timestamp('maintenance_due_at')->nullable()->after('last_maintained_at');
        });
    }

    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn(['last_maintained_at', 'maintenance_due_at']);
        });
    }
};