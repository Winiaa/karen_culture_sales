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
        Schema::table('drivers', function (Blueprint $table) {
            // Drop existing columns that we don't need
            $table->dropColumn([
                'vehicle_number',
                'status',
                'current_latitude',
                'current_longitude',
                'last_location_update'
            ]);

            // Add new columns
            $table->string('phone_number')->after('user_id');
            $table->string('vehicle_plate')->nullable()->after('vehicle_type');
            $table->decimal('rating', 3, 2)->default(5.00)->after('is_active');
            $table->integer('total_deliveries')->default(0)->after('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // Restore original columns
            $table->string('vehicle_number')->after('vehicle_type');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('inactive')->after('license_number');
            $table->decimal('current_latitude', 10, 8)->nullable();
            $table->decimal('current_longitude', 11, 8)->nullable();
            $table->timestamp('last_location_update')->nullable();

            // Remove new columns
            $table->dropColumn([
                'phone_number',
                'vehicle_plate',
                'rating',
                'total_deliveries'
            ]);
        });
    }
}; 