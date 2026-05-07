<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update the status enum to include 'claimed'
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','washing','drying','ready','claimed','completed','cancelled') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','washing','drying','ready','completed','cancelled') DEFAULT 'pending'");
    }
};