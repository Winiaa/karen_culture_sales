<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Basic statistics
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalUsers = User::count();
        $totalCategories = Category::count();
        
        // Recent orders
        $latestOrders = Order::with(['user'])
            ->latest()
            ->take(5)
            ->get();
            
        // Top products (by order count)
        $topProducts = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();
            
        // Monthly revenue (optional)
        $currentMonth = now()->startOfMonth();
        \Log::info('Current month: ' . $currentMonth);
        
        $revenueQuery = Order::where('created_at', '>=', $currentMonth)
            ->where('payment_status', 'completed');
            
        \Log::info('Revenue query SQL: ' . $revenueQuery->toSql());
        \Log::info('Revenue query bindings: ' . json_encode($revenueQuery->getBindings()));
        
        $monthlyRevenue = $revenueQuery->sum('total_amount');
        \Log::info('Monthly revenue calculated: ' . $monthlyRevenue);
            
        // Sales overview for chart (optional)
        $salesData = [];
        
        // For debugging purposes
        $debugData = [];
        
        // Calculate sales for last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            
            // Get the sales data directly with explicit casting
            $salesAmount = (float) Order::whereDate('created_at', $date)
                ->where('payment_status', 'completed')
                ->sum('total_amount');
                
            // Force numeric representation
            $salesAmount = number_format($salesAmount, 2, '.', '');
            
            // Log the exact value for today
            if ($i == 0) {
                \Log::info("TODAY'S SALES AMOUNT: " . $salesAmount . " (type: " . gettype($salesAmount) . ")");
            }
            
            $salesData[] = [
                'date' => now()->subDays($i)->format('D'),
                'sales' => (float) $salesAmount
            ];
        }
        
        // Log detailed debug info
        \Log::info('DETAILED SALES DEBUG DATA: ' . json_encode($debugData));
        \Log::info('FINAL SALES DATA: ' . json_encode($salesData));

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalProducts',
            'totalUsers',
            'totalCategories',
            'latestOrders',
            'topProducts',
            'monthlyRevenue',
            'salesData'
        ));
    }
}
