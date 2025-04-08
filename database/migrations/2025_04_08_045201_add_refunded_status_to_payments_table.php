<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to modify the enum directly
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the enum to its original state
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending'");
    }
};
