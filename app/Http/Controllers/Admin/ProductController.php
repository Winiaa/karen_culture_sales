<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')
            ->paginate(20);

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
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $this->validateProduct($request);
            
            // Generate slug from title
            $data['slug'] = Str::slug($request->title);
            
            // Process and store the main image
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                
                // Make sure the directory exists
                if (!Storage::disk('public')->exists('products')) {
                    Storage::disk('public')->makeDirectory('products');
                }
                
                $imagePath = $image->storeAs('products', $filename, 'public');
                $data['image'] = $imagePath;
            }
            
            // Process and store additional images
            $additionalImages = [];
            if ($request->hasFile('additional_images')) {
                foreach ($request->file('additional_images') as $index => $image) {
                    $filename = time() . '_' . $index . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                    
                    // Make sure the directory exists
                    if (!Storage::disk('public')->exists('products/additional')) {
                        Storage::disk('public')->makeDirectory('products/additional');
                    }
                    
                    $imagePath = $image->storeAs('products/additional', $filename, 'public');
                    $additionalImages[] = $imagePath;
                }
                
                $data['additional_images'] = $additionalImages;
            }
            
            // Set the status
            $data['status'] = $request->status;
            
            // Create product
            $product = Product::create($data);
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            \Log::error('Error creating product: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating product: ' . $e->getMessage());
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
        try {
            $data = $this->validateProduct($request, true);
            
            // Handle main image if a new one is uploaded
            if ($request->hasFile('image')) {
                // Delete old image if it exists
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                
                $image = $request->file('image');
                $filename = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                
                // Make sure the directory exists
                if (!Storage::disk('public')->exists('products')) {
                    Storage::disk('public')->makeDirectory('products');
                }
                
                $imagePath = $image->storeAs('products', $filename, 'public');
                $data['image'] = $imagePath;
            }
            
            // Handle additional images
            if ($request->hasFile('additional_images')) {
                // Get existing additional images
                $existingImages = $product->additional_images ?? [];
                
                // Process and store new additional images
                foreach ($request->file('additional_images') as $index => $image) {
                    $filename = time() . '_' . $index . '_' . str_replace(' ', '_', $image->getClientOriginalName());
                    
                    // Make sure the directory exists
                    if (!Storage::disk('public')->exists('products/additional')) {
                        Storage::disk('public')->makeDirectory('products/additional');
                    }
                    
                    $imagePath = $image->storeAs('products/additional', $filename, 'public');
                    $existingImages[] = $imagePath;
                }
                
                $data['additional_images'] = $existingImages;
            }
            
            // Handle deleted images if any
            if ($request->has('delete_images') && is_array($request->delete_images)) {
                $currentImages = $product->additional_images ?? [];
                $imagesToKeep = [];
                
                foreach ($currentImages as $imagePath) {
                    if (!in_array($imagePath, $request->delete_images)) {
                        $imagesToKeep[] = $imagePath;
                    } else {
                        // Delete the file from storage
                        if (Storage::disk('public')->exists($imagePath)) {
                            Storage::disk('public')->delete($imagePath);
                        }
                    }
                }
                
                $data['additional_images'] = $imagesToKeep;
            }
            
            // Update product
            $product->update($data);
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating product: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        try {
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
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting product: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error deleting product: ' . $e->getMessage());
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
}
