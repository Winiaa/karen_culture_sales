<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the cart items.
     */
    public function index()
    {
        $cartItems = Cart::where('user_id', auth()->id())
            ->with('product.category')
            ->whereHas('product', function($query) {
                $query->where('status', 'active');
            })
            ->get();

        // Remove any cart items with inactive products
        Cart::where('user_id', auth()->id())
            ->whereHas('product', function($query) {
                $query->where('status', '!=', 'active');
            })
            ->delete();

        $total = $cartItems->sum(function($item) {
            return $item->quantity * $item->product->final_price;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * Add a product to cart.
     */
    public function add(Request $request, Product $product)
    {
        // Check if product is active
        if ($product->status !== 'active') {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product is no longer available.'
                ], 400);
            }
            return back()->with('error', 'This product is no longer available.');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->quantity,
        ]);

        $cartItem = Cart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $request->quantity
            ]);
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity
            ]);
        }

        // Get the total number of items in the cart for the cart count badge
        $cartCount = Cart::where('user_id', auth()->id())->sum('quantity');

        // Check if it's an AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Alright! Product added to your cart successfully.',
                'cart_count' => $cartCount
            ]);
        }

        return back()->with('success', 'Product added to cart successfully.');
    }

    /**
     * Update the quantity of a cart item.
     */
    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $cart->product->quantity,
        ]);

        // Update the cart item quantity
        $cart->update([
            'quantity' => $request->quantity
        ]);

        return back()->with('success', 'Cart quantity updated successfully.');
    }

    /**
     * Remove a cart item.
     */
    public function remove(Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }

        $cart->delete();

        return redirect()->route('cart.index')
            ->with('success', 'Item removed from cart.');
    }

    /**
     * Clear all items from the user's cart.
     */
    public function clear()
    {
        Cart::where('user_id', auth()->id())->delete();
        
        \Log::info('Cart cleared for user #' . auth()->id());
        
        return redirect()->route('cart.index')
            ->with('success', 'Your cart has been cleared.');
    }
}
