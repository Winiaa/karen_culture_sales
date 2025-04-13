<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete only Stripe orders with pending payment
        Order::whereHas('payment', function($query) {
            $query->where('payment_method', 'stripe')
                  ->where('payment_status', 'pending');
        })->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to restore deleted orders
    }
};
