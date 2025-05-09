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
        Schema::table('carts', function (Blueprint $table) {
            // Make user_id nullable for guest carts
            $table->foreignId('user_id')->nullable()->change();
            
            // Add session and device tracking
            $table->string('session_id', 40)->nullable()->after('quantity');
            $table->string('device_id', 40)->nullable()->after('session_id');
            
            // Add indexes
            $table->index('session_id');
            $table->index('device_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Remove indexes
            $table->dropIndex(['session_id']);
            $table->dropIndex(['device_id']);
            
            // Remove columns
            $table->dropColumn(['session_id', 'device_id']);
            
            // Make user_id required again
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
}; 