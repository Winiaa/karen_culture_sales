<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function home()
    {
        $featuredProducts = $this->productService->getFeaturedProducts(8)->get();

        $categories = Category::where('is_active', 1)
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(6)
            ->get();

        return view('home', compact('featuredProducts', 'categories'));
    }

    public function index(Request $request)
    {
        $request->validate([
            'category' => 'nullable|exists:categories,id',
            'sort' => 'nullable|in:price_asc,price_desc,popular,newest',
            'search' => 'nullable|string|max:100',
        ]);

        $query = $this->productService->getBaseQuery();
        $query = $this->productService->applyCategory($query, $request->category);
        $query = $this->productService->applySearch($query, $request->search);
        $query = $this->productService->applySort($query, $request->sort);

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', 1)->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        if (!$product->category || $product->status !== 'active') {
            return redirect()->route('products.index');
        }

        $product->load(['category', 'reviews.user']);

        $relatedProducts = $this->productService->getBaseQuery()
            ->where('products.category_id', $product->category_id)
            ->where('products.id', '!=', $product->id)
            ->take(5)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function categories()
    {
        $categories = Category::where('is_active', 1)
            ->withCount('products')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function categoryProducts(Request $request, Category $category)
    {
        if (!$category->is_active) {
            return redirect()->route('categories.index')->with('error', 'This category is not available.');
        }

        // ✅ Validation for sorting/searching
        $request->validate([
            'sort' => 'nullable|in:price_asc,price_desc,newest',
            'search' => 'nullable|string|max:100',
        ]);

        $query = $category->products()->where('products.status', 'active');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('products.title', 'like', "%{$request->search}%")
                  ->orWhere('products.description', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('products.price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('products.price', 'desc');
                    break;
                default:
                    $query->orderBy('products.created_at', 'desc');
            }
        } else {
            $query->orderBy('products.created_at', 'desc');
        }

        $products = $query->paginate(12)->appends($request->query());

        return view('categories.show', compact('category', 'products'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|max:100',
        ]);

        $query = $this->productService->getBaseQuery();
        $query = $this->productService->applySearch($query, $request->q);
        $products = $query->take(5)->get();

        if ($request->ajax()) {
            return response()->json($products);
        }

        return view('products.search', compact('products', 'search'));
    }

    public function newArrivals(Request $request)
    {
        $request->validate([
            'category' => 'nullable|exists:categories,id',
            'search' => 'nullable|string|max:100',
            'sort' => 'nullable|in:price_asc,price_desc,popular,newest',
        ]);

        $query = $this->productService->getNewArrivals();
        $query = $this->productService->applyCategory($query, $request->category);
        $query = $this->productService->applySearch($query, $request->search);
        $query = $this->productService->applySort($query, $request->sort);

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', 1)->get();

        return view('products.new-arrivals', compact('products', 'categories'));
    }

    public function bestSellers(Request $request)
    {
        $request->validate([
            'category' => 'nullable|exists:categories,id',
            'search' => 'nullable|string|max:100',
            'sort' => 'nullable|in:price_asc,price_desc,newest,popular',
        ]);

        $topBestSellerIds = $this->productService->getTopBestSellerIds();

        $query = $this->productService->getBestSellers();
        $query = $this->productService->applyCategory($query, $request->category);
        $query = $this->productService->applySearch($query, $request->search);
        $query = $this->productService->applySort($query, $request->sort, true);

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', 1)->get();

        return view('products.best-sellers', compact('products', 'categories', 'topBestSellerIds'));
    }

}
