<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeletePendingStripeOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:delete-pending-stripe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete orders with pending Stripe payments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to process pending Stripe orders...');
        
        try {
            DB::beginTransaction();
            
            // Find orders with pending Stripe payments
            $orders = Order::whereHas('payment', function($query) {
                $query->where('payment_method', 'stripe')
                      ->where('payment_status', 'pending');
            })->with(['payment', 'orderItems'])->get();
            
            if ($orders->isEmpty()) {
                $this->info('No pending Stripe orders found.');
                DB::commit();
                return;
            }
            
            $this->info(sprintf('Found %d pending Stripe orders.', $orders->count()));
            
            foreach ($orders as $order) {
                $this->info(sprintf('Processing Order #%d...', $order->id));
                
                // Delete related records first
                $order->orderItems()->delete();
                $order->payment()->delete();
                $order->delete();
                
                $this->info(sprintf('Order #%d deleted successfully.', $order->id));
                Log::info('Deleted pending Stripe order', ['order_id' => $order->id]);
            }
            
            DB::commit();
            $this->info('All pending Stripe orders have been deleted successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting pending Stripe orders', ['error' => $e->getMessage()]);
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }
}
