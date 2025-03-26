<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProductController extends Controller
{
    /**
     * Display the home page with featured products.
     */
    public function home()
    {
        $featuredProducts = Product::with('category')
            ->whereHas('category', function($query) {
                $query->where('is_active', 1);
            })
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $categories = Category::where('is_active', 1)
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(4)
            ->get();

        return view('home', compact('featuredProducts', 'categories'));
    }

    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->whereHas('category', function($query) {
                $query->where('is_active', 1);
            })
            ->where('status', 'active');

        if ($request->has('category')) {
            $categoryId = $request->category;
            // Check if the requested category is active
            $isActive = Category::where('id', $categoryId)->where('is_active', 1)->exists();
            if ($isActive) {
                $query->where('category_id', $categoryId);
            } else {
                // If category is inactive, redirect with error
                return redirect()->route('products.index')
                    ->with('error', 'The selected category is not available.');
            }
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'popular':
                    // Join with order_items table to count how many times a product has been ordered
                    $query->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                          ->select('products.*', \DB::raw('COUNT(order_items.id) as order_count'))
                          ->groupBy('products.id')
                          ->orderBy('order_count', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        }

        $products = $query->paginate(12);
        $categories = Category::where('is_active', 1)->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        // Check if the product belongs to the category and is active
        if (!$product->category || $product->status !== 'active') {
            return redirect()->route('products.index');
        }

        // Load the product relationships
        $product->load(['category', 'reviews.user']);

        // Get related products (same category, exclude current)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->whereHas('category', function($query) {
                $query->where('is_active', 1);
            })
            ->take(5)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Display a listing of the categories.
     */
    public function categories()
    {
        $categories = Category::where('is_active', 1)->withCount('products')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Display products for a specific category.
     */
    public function categoryProducts(Category $category)
    {
        // Redirect to categories page if the category is inactive
        if (!$category->is_active) {
            return redirect()->route('categories.index')
                ->with('error', 'This category is not available.');
        }
        
        $products = $category->products()->paginate(12);
        return view('categories.show', compact('category', 'products'));
    }

    /**
     * Search for products.
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        $products = Product::where(function($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->where('status', 'active')
            ->whereHas('category', function($query) {
                $query->where('is_active', 1);
            })
            ->with('category')
            ->take(5)
            ->get();

        if ($request->ajax()) {
            return response()->json($products);
        }

        return view('products.search', compact('products', 'search'));
    }

    /**
     * Display newest products.
     */
    public function newArrivals()
    {
        $products = Product::with('category')
            ->whereHas('category', function($query) {
                $query->where('is_active', 1);
            })
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        $categories = Category::where('is_active', 1)->get();
        
        return view('products.new-arrivals', compact('products', 'categories'));
    }
    
    /**
     * Display best selling products.
     */
    public function bestSellers()
    {
        $products = Product::with('category')
            ->whereHas('category', function($query) {
                $query->where('is_active', 1);
            })
            ->where('status', 'active')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->select('products.*', \DB::raw('COUNT(order_items.id) as order_count'))
            ->groupBy('products.id')
            ->orderBy('order_count', 'desc')
            ->paginate(12);
            
        $categories = Category::where('is_active', 1)->get();
        
        return view('products.best-sellers', compact('products', 'categories'));
    }
}
