<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('transfer_proof')->nullable()->after('notes');
            $table->enum('payment_status', ['pending', 'received', 'verified'])->default('pending')->after('transfer_proof');
            $table->timestamp('payment_received_at')->nullable()->after('payment_status');
            $table->text('payment_notes')->nullable()->after('payment_received_at');
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn(['transfer_proof', 'payment_status', 'payment_received_at', 'payment_notes']);
        });
    }
}; 