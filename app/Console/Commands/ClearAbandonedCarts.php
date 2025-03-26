<?php

namespace App\Console\Commands;

use App\Models\Cart;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ClearAbandonedCarts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:clear-abandoned';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear abandoned cart items for orders with failed or abandoned payments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up abandoned carts...');
        
        // Find orders with pending payments that are older than 1 hour
        $cutoffTime = Carbon::now()->subHour();
        
        $pendingOrders = Order::where('payment_status', 'pending')
            ->where('created_at', '<', $cutoffTime)
            ->get();
            
        $this->info('Found ' . $pendingOrders->count() . ' abandoned orders.');
        
        foreach ($pendingOrders as $order) {
            // Clean up cart items for the user
            $cartItems = Cart::where('user_id', $order->user_id)->delete();
            
            $this->info('Cleared cart for user #' . $order->user_id . ' with abandoned order #' . $order->id);
            
            // Update order status to indicate it was abandoned
            $order->update([
                'order_status' => 'cancelled',
                'payment_status' => 'failed'
            ]);
            
            // Restore product quantities
            foreach ($order->orderItems as $item) {
                $item->product->increment('quantity', $item->quantity);
                $this->info('Restored ' . $item->quantity . ' units to product #' . $item->product_id);
            }
        }
        
        $this->info('Abandoned cart cleanup completed.');
        
        return Command::SUCCESS;
    }
}
