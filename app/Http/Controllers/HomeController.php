<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        // Only redirect admins, allow other users to see the home page
        if (auth()->check() && auth()->user()->usertype === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        try {
            // Get top 9 categories
            $categories = Category::where('is_active', 1)
                ->withCount('products')
                ->orderBy('products_count', 'desc')
                ->take(9)
                ->get();
            Log::info('Categories loaded', ['count' => $categories->count()]);

            // Get featured products (for now, we'll just get the latest 4 products)
            $featuredProducts = Product::with('category')
                ->whereHas('category', function($query) {
                    $query->where('is_active', 1);
                })
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get();
            Log::info('Featured products loaded', ['count' => $featuredProducts->count()]);

            return view('home', compact('categories', 'featuredProducts'));
        } catch (\Exception $e) {
            Log::error('Error in HomeController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return an empty array if there's an error to prevent undefined variable
            return view('home', [
                'categories' => collect([]),
                'featuredProducts' => collect([])
            ]);
        }
    }
} 