<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->decimal('capacity_kg', 5, 2)->default(7.00)->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn('capacity_kg');
        });
    }
};
