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
        // First, update any existing records to use the new status values
        DB::table('deliveries')
            ->where('delivery_status', 'out_for_delivery')
            ->update(['delivery_status' => 'pending']);

        // Then modify the enum values
        DB::statement("ALTER TABLE deliveries MODIFY COLUMN delivery_status ENUM('pending', 'assigned', 'picked_up', 'out_for_delivery', 'delivered', 'failed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, update any records with new status values back to old ones
        DB::table('deliveries')
            ->whereIn('delivery_status', ['assigned', 'picked_up', 'failed'])
            ->update(['delivery_status' => 'pending']);

        // Then revert the enum values
        DB::statement("ALTER TABLE deliveries MODIFY COLUMN delivery_status ENUM('pending', 'out_for_delivery', 'delivered') DEFAULT 'pending'");
    }
}; 