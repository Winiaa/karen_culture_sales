<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\NewsletterService;

class ProductController extends Controller
{
    protected $newsletterService;

    public function __construct(NewsletterService $newsletterService)
    {
        $this->newsletterService = $newsletterService;
    }

    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $categoryId = $request->get('category');
        $search = $request->get('search');
        
        // Create a unique cache key based on the filters
        $cacheKey = sprintf(
            'admin.products.%s.%s.%s',
            $categoryId ?? 'all',
            $search ?? 'no-search',
            $request->get('page', 1)
        );

        $products = Cache::remember($cacheKey, 3600, function () use ($categoryId, $search, $cacheKey) {
            // Add this key to our tracking list
            $this->addProductCacheKey($cacheKey);

            $query = Product::with('category');

            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            return $query->orderBy('created_at', 'desc')
                ->paginate(20);
        });

        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Validate product data from the request.
     */
    private function validateProduct(Request $request, $update = false)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive',
            'image' => $update ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        return $request->validate($rules);
    }

    /**
     * Generate a unique slug for the product
     */
    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $count = 1;
        
        // Keep checking if the slug exists and append a number if it does
        while (Product::where('slug', $slug)->exists()) {
            $slug = Str::slug($title) . '-' . $count;
            $count++;
        }
        
        return $slug;
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Create image manager
            $manager = new ImageManager(new Driver());

            // Handle main image
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . $image->getClientOriginalName();
                
                // Optimize and save the image
                $img = $manager->read($image);
                
                // Get original dimensions
                $width = $img->width();
                $height = $img->height();
                
                // Calculate new dimensions while maintaining aspect ratio
                $maxDimension = 800;
                if ($width > $height) {
                    $newWidth = $maxDimension;
                    $newHeight = floor($height * ($maxDimension / $width));
                } else {
                    $newHeight = $maxDimension;
                    $newWidth = floor($width * ($maxDimension / $height));
                }
                
                // Resize image
                $img->resize($newWidth, $newHeight)
                    ->save(storage_path('app/public/products/' . $filename));
                
                $imagePath = 'products/' . $filename;
            }

            // Handle additional images
            $additionalImages = [];
            if ($request->hasFile('additional_images')) {
                foreach ($request->file('additional_images') as $image) {
                    $filename = time() . '_' . $image->getClientOriginalName();
                    
                    // Optimize and save each additional image
                    $img = $manager->read($image);
                    
                    // Get original dimensions
                    $width = $img->width();
                    $height = $img->height();
                    
                    // Calculate new dimensions while maintaining aspect ratio
                    $maxDimension = 800;
                    if ($width > $height) {
                        $newWidth = $maxDimension;
                        $newHeight = floor($height * ($maxDimension / $width));
                    } else {
                        $newHeight = $maxDimension;
                        $newWidth = floor($width * ($maxDimension / $height));
                    }
                    
                    // Resize image
                    $img->resize($newWidth, $newHeight)
                        ->save(storage_path('app/public/products/' . $filename));
                    
                    $additionalImages[] = 'products/' . $filename;
                }
            }

            // Generate unique slug
            $slug = $this->generateUniqueSlug($request->title);

            // Create product
            $product = Product::create([
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'quantity' => $request->quantity,
                'category_id' => $request->category_id,
                'image' => $imagePath ?? null,
                'additional_images' => !empty($additionalImages) ? $additionalImages : null,
                'status' => $request->status ?? 'active'
            ]);

            // Clear all product-related cache
            $this->clearProductCache();

            // Send notifications to subscribers if the product is active
            if ($product->status === 'active') {
                $this->newsletterService->notifyNewProduct($product);
            }

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating product: ' . $e->getMessage());
            return back()->with('error', 'Failed to create product. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'reviews.user']);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Create image manager
            $manager = new ImageManager(new Driver());

            // Handle main image
            if ($request->hasFile('image')) {
                // Delete old image
                if ($product->image) {
                    Storage::delete('public/' . $product->image);
                }

                $image = $request->file('image');
                $filename = time() . '_' . $image->getClientOriginalName();
                
                // Optimize and save the new image
                $img = $manager->read($image);
                
                // Get original dimensions
                $width = $img->width();
                $height = $img->height();
                
                // Calculate new dimensions while maintaining aspect ratio
                $maxDimension = 800;
                if ($width > $height) {
                    $newWidth = $maxDimension;
                    $newHeight = floor($height * ($maxDimension / $width));
                } else {
                    $newHeight = $maxDimension;
                    $newWidth = floor($width * ($maxDimension / $height));
                }
                
                // Resize image
                $img->resize($newWidth, $newHeight)
                    ->save(storage_path('app/public/products/' . $filename));
                
                $imagePath = 'products/' . $filename;
            } else {
                $imagePath = $product->image;
            }

            // Handle additional images
            $additionalImages = $product->additional_images ?? [];
            
            // Handle deletion of additional images
            if ($request->has('delete_images') && is_array($request->delete_images)) {
                // Filter out any null values and empty strings from the delete_images array
                $deleteImages = array_filter($request->delete_images, function($value) {
                    return $value !== null && $value !== '';
                });
                
                // Log for debugging
                \Illuminate\Support\Facades\Log::info('Images to delete: ' . json_encode($deleteImages));
                \Illuminate\Support\Facades\Log::info('Current additional images: ' . json_encode($additionalImages));
                
                foreach ($deleteImages as $imageToDelete) {
                    // Remove from storage
                    if (Storage::disk('public')->exists($imageToDelete)) {
                        Storage::disk('public')->delete($imageToDelete);
                        \Illuminate\Support\Facades\Log::info('Deleted image from storage: ' . $imageToDelete);
                    }
                    
                    // Remove from array
                    $additionalImages = array_filter($additionalImages, function($img) use ($imageToDelete) {
                        return $img !== $imageToDelete;
                    });
                }
                
                // Reindex the array to ensure sequential keys
                $additionalImages = array_values($additionalImages);
                
                // Log for debugging
                \Illuminate\Support\Facades\Log::info('Remaining additional images: ' . json_encode($additionalImages));
            }
            
            // Handle new additional images
            if ($request->hasFile('additional_images')) {
                foreach ($request->file('additional_images') as $image) {
                    $filename = time() . '_' . $image->getClientOriginalName();
                    
                    // Optimize and save each additional image
                    $img = $manager->read($image);
                    
                    // Get original dimensions
                    $width = $img->width();
                    $height = $img->height();
                    
                    // Calculate new dimensions while maintaining aspect ratio
                    $maxDimension = 800;
                    if ($width > $height) {
                        $newWidth = $maxDimension;
                        $newHeight = floor($height * ($maxDimension / $width));
                    } else {
                        $newHeight = $maxDimension;
                        $newWidth = floor($width * ($maxDimension / $height));
                    }
                    
                    // Resize image
                    $img->resize($newWidth, $newHeight)
                        ->save(storage_path('app/public/products/' . $filename));
                    
                    $additionalImages[] = 'products/' . $filename;
                }
            }

            // Update product
            $product->update([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'description' => $request->description,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'quantity' => $request->quantity,
                'category_id' => $request->category_id,
                'image' => $imagePath,
                'additional_images' => !empty($additionalImages) ? array_values($additionalImages) : null,
                'status' => $request->status ?? 'active'
            ]);

            // Clear all product-related cache
            $this->clearProductCache();

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating product: ' . $e->getMessage());
            return back()->with('error', 'Failed to update product. Please try again.');
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            // Delete main image if it exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            
            // Delete additional images if they exist
            if (!empty($product->additional_images)) {
                foreach ($product->additional_images as $imagePath) {
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                }
            }
            
            $product->delete();
            
            // Clear all product-related cache
            $this->clearProductCache();

            DB::commit();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting product: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete product. Please try again.');
        }
    }

    /**
     * Display a listing of the reviews.
     */
    public function reviews(Request $request)
    {
        $query = Review::with(['user', 'product']);

        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->has('product')) {
            $query->where('product_id', $request->product);
        }

        $reviews = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Remove the specified review from storage.
     */
    public function deleteReview(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }

    /**
     * Toggle product availability.
     */
    public function toggle(Product $product)
    {
        $product->update([
            'quantity' => $product->quantity > 0 ? 0 : 1
        ]);

        return back()->with('success', 'Product availability updated successfully.');
    }

    private function clearProductCache()
    {
        // Get all cache keys
        $keys = Cache::get('product_cache_keys', []);
        
        // Clear each key
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        
        // Clear the keys list
        Cache::forget('product_cache_keys');
    }

    private function addProductCacheKey($key)
    {
        $keys = Cache::get('product_cache_keys', []);
        if (!in_array($key, $keys)) {
            $keys[] = $key;
            Cache::forever('product_cache_keys', $keys);
        }
    }
}
