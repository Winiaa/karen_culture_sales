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
            if (!Schema::hasColumn('deliveries', 'is_confirmed_by_customer')) {
                $table->boolean('is_confirmed_by_customer')->default(false)->after('delivery_status');
            }
            
            if (!Schema::hasColumn('deliveries', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('is_confirmed_by_customer');
            }
            
            if (!Schema::hasColumn('deliveries', 'delivery_photo')) {
                $table->text('delivery_photo')->nullable()->after('confirmed_at');
            }
            
            if (!Schema::hasColumn('deliveries', 'delivery_notes')) {
                $table->text('delivery_notes')->nullable()->after('delivery_photo');
            }
            
            // Update the delivery_status enum to include more states
            $table->enum('delivery_status', [
                'pending', 
                'assigned', 
                'picked_up', 
                'out_for_delivery', 
                'delivered', 
                'failed'
            ])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn([
                'is_confirmed_by_customer',
                'confirmed_at',
                'delivery_photo',
                'delivery_notes'
            ]);
            
            // Revert the delivery_status enum
            $table->enum('delivery_status', [
                'pending', 
                'out_for_delivery', 
                'delivered'
            ])->default('pending')->change();
        });
    }
};
