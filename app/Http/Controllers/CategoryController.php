<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = Category::where('is_active', 1)
            ->withCount(['products' => function($query) {
                $query->where('status', 'active');
            }])
            ->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Display the specified category.
     */
    public function show(Request $request, Category $category)
    {
        // Redirect to categories page if the category is inactive
        if (!$category->is_active) {
            return redirect()->route('categories.index')
                ->with('error', 'This category is not available.');
        }

        // Validate incoming filter/sort
        $request->validate([
            'sort' => 'nullable|in:price_asc,price_desc,newest,popular',
            'search' => 'nullable|string|max:100',
        ]);

        $query = $category->products()
            ->where('status', 'active');

        // Apply search filter
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        // Apply sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'popular':
                    $query->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                          ->select('products.*', DB::raw('COUNT(order_items.id) as order_count'))
                          ->groupBy('products.id')
                          ->orderBy('order_count', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();

        return view('categories.show', compact('category', 'products'));
    }
} 