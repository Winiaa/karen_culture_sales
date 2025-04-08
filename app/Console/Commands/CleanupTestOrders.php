<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupTestOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cleanup-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up test orders with pending payment status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of test orders...');
        
        // Get the orders to delete
        $orders = Order::whereIn('id', [4, 5, 6, 7])
                      ->where('payment_status', 'pending')
                      ->get();
        
        if ($orders->isEmpty()) {
            $this->warn('No matching test orders found to clean up.');
            return;
        }
        
        $this->info('Found ' . $orders->count() . ' test orders to clean up.');
        
        // Display the orders that will be deleted
        $this->table(
            ['Order ID', 'Customer', 'Date', 'Total', 'Status', 'Payment'],
            $orders->map(function ($order) {
                return [
                    '#' . $order->id,
                    $order->user->name ?? 'Unknown',
                    $order->created_at->format('M d, Y'),
                    '฿' . number_format($order->total_amount, 2),
                    $order->order_status,
                    $order->payment_status
                ];
            })
        );
        
        // Ask for confirmation
        if (!$this->confirm('Are you sure you want to delete these orders? This action cannot be undone.')) {
            $this->info('Cleanup cancelled.');
            return;
        }
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            $count = 0;
            
            foreach ($orders as $order) {
                // Log the deletion
                Log::info('Deleting test order', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'total_amount' => $order->total_amount,
                    'payment_status' => $order->payment_status,
                    'order_status' => $order->order_status
                ]);
                
                // Delete the order (this will cascade to order items and payment)
                $order->delete();
                $count++;
            }
            
            DB::commit();
            $this->info("Successfully deleted {$count} test orders.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('An error occurred while deleting orders: ' . $e->getMessage());
            Log::error('Failed to delete test orders: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }
} 