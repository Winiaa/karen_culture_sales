<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            // Drop existing column if it exists
            if (Schema::hasColumn('deliveries', 'estimated_delivery_time')) {
                $table->dropColumn('estimated_delivery_time');
            }
            
            // Add new columns if they don't exist
            if (!Schema::hasColumn('deliveries', 'carrier')) {
                $table->string('carrier')->nullable()->after('tracking_number');
            }
            
            if (!Schema::hasColumn('deliveries', 'estimated_delivery_date')) {
                $table->date('estimated_delivery_date')->nullable()->after('carrier');
            }
            
            if (!Schema::hasColumn('deliveries', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('estimated_delivery_date');
            }
            
            if (!Schema::hasColumn('deliveries', 'notes')) {
                $table->text('notes')->nullable()->after('recipient_address');
            }
            
            // Make tracking number nullable
            $table->string('tracking_number')->nullable()->change();
            
            // Make user_id nullable
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            // Drop columns if they exist
            $columns = ['carrier', 'estimated_delivery_date', 'delivered_at', 'notes'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('deliveries', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            if (!Schema::hasColumn('deliveries', 'estimated_delivery_time')) {
                $table->timestamp('estimated_delivery_time')->nullable()->after('tracking_number');
            }
            
            $table->string('tracking_number')->change();
            $table->foreignId('user_id')->change();
        });
    }
}; 