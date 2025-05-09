<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Basic statistics
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalUsers = User::count();
        $totalCategories = Category::count();
        
        // Recent orders - load user relationship
        $latestOrders = Order::with(['user'])
            ->latest()
            ->take(10)
            ->get();
            
        // Top products (by order count) - load category relationship
        $topProducts = Product::with(['category'])
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();
            
        // Monthly revenue
        $currentMonth = now()->startOfMonth();
        Log::info('Current month: ' . $currentMonth);
        
        $revenueQuery = Order::where('created_at', '>=', $currentMonth)
            ->where('payment_status', 'completed')
            ->where('order_status', '!=', 'cancelled');
            
        Log::info('Revenue query SQL: ' . $revenueQuery->toSql());
        Log::info('Revenue query bindings: ' . json_encode($revenueQuery->getBindings()));
        
        $monthlyRevenue = $revenueQuery->sum('total_amount');
        Log::info('Monthly revenue calculated: ' . $monthlyRevenue);
        
        // Today's revenue
        $todayRevenue = Order::whereDate('created_at', today())
            ->where('payment_status', 'completed')
            ->where('order_status', '!=', 'cancelled')
            ->sum('total_amount');
            
        // Get completed orders count for average calculation
        $completedOrdersCount = Order::where('payment_status', 'completed')
            ->where('order_status', '!=', 'cancelled')
            ->count();
            
        // Average order value
        $averageOrderValue = $completedOrdersCount > 0 
            ? Order::where('payment_status', 'completed')
                ->where('order_status', '!=', 'cancelled')
                ->sum('total_amount') / $completedOrdersCount 
            : 0;
            
        // Yearly revenue
        $yearlyRevenue = Order::whereYear('created_at', now()->year)
            ->where('payment_status', 'completed')
            ->where('order_status', '!=', 'cancelled')
            ->sum('total_amount');
            
        // Sales overview for chart
        $salesData = [];
        
        // Calculate sales for last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            
            // Get the sales data for this day
            $salesAmount = Order::whereDate('created_at', $date)
                ->where('payment_status', 'completed')
                ->where('order_status', '!=', 'cancelled')
                ->sum('total_amount');
                
            // Convert to float and ensure it's a number
            $salesAmount = (float) $salesAmount;
            
            // Log the exact value for today
            if ($i == 0) {
                Log::info("TODAY'S SALES AMOUNT: " . $salesAmount);
            }
            
            $salesData[] = [
                'date' => now()->subDays($i)->format('D'),
                'sales' => $salesAmount
            ];
        }
        
        // Log the final sales data
        Log::info('FINAL SALES DATA: ' . json_encode($salesData));

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalProducts',
            'totalUsers',
            'totalCategories',
            'latestOrders',
            'topProducts',
            'monthlyRevenue',
            'todayRevenue',
            'averageOrderValue',
            'yearlyRevenue',
            'salesData'
        ));
    }
} 