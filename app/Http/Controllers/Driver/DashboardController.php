<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the driver dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();
        $driver = $user->driver;
        
        if (!$driver) {
            // If the user is marked as a driver but doesn't have a driver profile yet
            return view('driver.setup');
        }
        
        // Get active deliveries
        $activeDeliveries = $driver->activeDeliveries()->with('order')->latest()->take(5)->get();
        
        // Get completed deliveries
        $completedDeliveries = $driver->completedDeliveries()->with('order')->latest()->take(5)->get();
        
        // Get statistics
        $stats = [
            'total_deliveries' => $driver->completedDeliveries()->count(),
            'active_deliveries' => $driver->activeDeliveries()->count(),
            'completed_today' => $driver->completedDeliveries()
                ->whereDate('delivered_at', today())
                ->count(),
            'rating' => $driver->rating ?? 0,
            'is_active' => $driver->is_active,
        ];
        
        return view('driver.dashboard', [
            'driver' => $driver,
            'activeDeliveries' => $activeDeliveries,
            'completedDeliveries' => $completedDeliveries,
            'stats' => $stats,
        ]);
    }
}
