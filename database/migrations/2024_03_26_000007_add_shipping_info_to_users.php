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
        Schema::table('Users', function (Blueprint $table) {
            $table->string('default_recipient_name')->nullable()->after('email');
            $table->string('default_recipient_phone')->nullable()->after('default_recipient_name');
            $table->text('default_shipping_address')->nullable()->after('default_recipient_phone');
            $table->boolean('save_shipping_info')->default(false)->after('default_shipping_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Users', function (Blueprint $table) {
            $table->dropColumn([
                'default_recipient_name',
                'default_recipient_phone',
                'default_shipping_address',
                'save_shipping_info'
            ]);
        });
    }
}; 