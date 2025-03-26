<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

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
    public function show(Category $category)
    {
        // Redirect to categories page if the category is inactive
        if (!$category->is_active) {
            return redirect()->route('categories.index')
                ->with('error', 'This category is not available.');
        }
        
        $products = $category->products()
            ->where('status', 'active')
            ->paginate(12);
        return view('categories.show', compact('category', 'products'));
    }
} 