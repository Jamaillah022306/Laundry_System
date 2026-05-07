<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE machines MODIFY COLUMN status ENUM('available', 'in_use', 'under_maintenance') NOT NULL DEFAULT 'available'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE machines MODIFY COLUMN status ENUM('available', 'in_use') NOT NULL DEFAULT 'available'");
    }
};
