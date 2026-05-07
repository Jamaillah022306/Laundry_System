<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Fix blank/null status orders — set to 'completed'
        DB::statement("
            UPDATE orders 
            SET status = 'completed' 
            WHERE status IS NULL OR status = ''
        ");

        // Step 2: Update ENUM to include 'archived'
        DB::statement("
            ALTER TABLE orders 
            MODIFY COLUMN status ENUM(
                'pending',
                'washing',
                'drying',
                'ready',
                'claimed',
                'completed',
                'cancelled',
                'archived'
            ) NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE orders 
            MODIFY COLUMN status ENUM(
                'pending',
                'washing',
                'drying',
                'ready',
                'claimed',
                'completed',
                'cancelled'
            ) NOT NULL DEFAULT 'pending'
        ");
    }
};