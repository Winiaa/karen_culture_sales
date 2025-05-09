<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the cart items.
     */
    public function index()
    {
        if (Auth::check()) {
            // Transfer session cart to database if user has session cart items
            $this->transferSessionCartToDatabase();

            $cartItems = Cart::where('user_id', Auth::id())
                ->with('product.category')
                ->whereHas('product', function($query) {
                    $query->where('status', 'active');
                })
                ->get();

            // Remove any cart items with inactive products
            Cart::where('user_id', Auth::id())
                ->whereHas('product', function($query) {
                    $query->where('status', '!=', 'active');
                })
                ->delete();
        } else {
            $cartItems = $this->getSessionCartItems();
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->quantity * $item->product->final_price;
        });

        // Calculate shipping cost
        $shippingCost = $subtotal >= config('shipping.free_shipping_threshold') ? 0 : config('shipping.default_shipping_cost');
        $total = $subtotal + $shippingCost;

        return view('cart.index', compact('cartItems', 'subtotal', 'shippingCost', 'total'));
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
            'quantity' => 'required|integer|min:1',
        ]);

        $requestedQty = $request->quantity;
        $currentCartQty = 0;

        if (Auth::check()) {
            $cartItem = Cart::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->first();
            if ($cartItem) {
                $currentCartQty = $cartItem->quantity;
            }
            $newTotal = $currentCartQty + $requestedQty;
            if ($newTotal > $product->quantity) {
                $maxCanAdd = max(0, $product->quantity - $currentCartQty);
                $message = $maxCanAdd > 0
                    ? "You can only add $maxCanAdd more of this product to your cart."
                    : "You already have the maximum available quantity of this product in your cart.";
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 400);
                }
                return back()->with('error', $message);
            }
            if ($cartItem) {
                $cartItem->update([
                    'quantity' => $newTotal
                ]);
            } else {
                Cart::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'quantity' => $requestedQty
                ]);
            }
        } else {
            $sessionCart = session('cart', []);
            $currentCartQty = $sessionCart[$product->id] ?? 0;
            $newTotal = $currentCartQty + $requestedQty;
            if ($newTotal > $product->quantity) {
                $maxCanAdd = max(0, $product->quantity - $currentCartQty);
                $message = $maxCanAdd > 0
                    ? "You can only add $maxCanAdd more of this product to your cart."
                    : "You already have the maximum available quantity of this product in your cart.";
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 400);
                }
                return back()->with('error', $message);
            }
            $sessionCart[$product->id] = $newTotal;
            session(['cart' => $sessionCart]);
        }

        $this->updateCartCount();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Alright! Product added to your cart successfully.',
                'cart_count' => session('cart_count', 0)
            ]);
        }

        return back()->with('success', 'Product added to cart successfully.');
    }

    /**
     * Update the quantity of a cart item.
     */
    public function update(Request $request, $cart)
    {
        if (Auth::check()) {
            $cartItem = Cart::findOrFail($cart);
            if ($cartItem->user_id !== Auth::id()) {
                abort(403);
            }

            $request->validate([
                'quantity' => 'required|integer|min:1|max:' . $cartItem->product->quantity,
            ]);

            $cartItem->update([
                'quantity' => $request->quantity
            ]);
        } else {
            $request->validate([
                'quantity' => 'required|integer|min:1|max:' . Product::findOrFail($cart)->quantity,
            ]);

            $sessionCart = session('cart', []);
            $sessionCart[$cart] = $request->quantity;
            session(['cart' => $sessionCart]);
        }

        $this->updateCartCount();

        return back()->with('success', 'Cart quantity updated successfully.');
    }

    /**
     * Remove a cart item.
     */
    public function remove($cart)
    {
        if (Auth::check()) {
            $cartItem = Cart::findOrFail($cart);
            if ($cartItem->user_id !== Auth::id()) {
                abort(403);
            }
            $cartItem->delete();
        } else {
            $sessionCart = session('cart', []);
            if (isset($sessionCart[$cart])) {
                unset($sessionCart[$cart]);
                session(['cart' => $sessionCart]);
            }
        }

        $this->updateCartCount();

        return redirect()->route('cart.index')
            ->with('success', 'Item removed from cart.');
    }

    /**
     * Clear all items from the cart.
     */
    public function clear()
    {
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->delete();
        } else {
            session()->forget('cart');
        }
        
        $this->updateCartCount(true);
        
        return redirect()->route('cart.index')
            ->with('success', 'Your cart has been cleared.');
    }

    /**
     * Get cart items from session for guest users
     */
    private function getSessionCartItems()
    {
        $cartItems = collect();
        $sessionCart = session('cart', []);
        
        foreach ($sessionCart as $productId => $quantity) {
            $product = Product::where('status', 'active')
                ->with('category')
                ->find($productId);
                
            if ($product) {
                $cartItems->push((object)[
                    'product' => $product,
                    'quantity' => $quantity,
                    'id' => $productId
                ]);
            }
        }

        return $cartItems;
    }

    /**
     * Transfer session cart items to database cart when user logs in
     */
    private function transferSessionCartToDatabase()
    {
        $sessionCart = session('cart', []);
        
        if (!empty($sessionCart)) {
            foreach ($sessionCart as $productId => $quantity) {
                $product = Product::where('status', 'active')->find($productId);
                
                if ($product) {
                    $existingCartItem = Cart::where('user_id', Auth::id())
                        ->where('product_id', $productId)
                        ->first();
                    
                    if ($existingCartItem) {
                        $existingCartItem->update([
                            'quantity' => $existingCartItem->quantity + $quantity
                        ]);
                    } else {
                        Cart::create([
                            'user_id' => Auth::id(),
                            'product_id' => $productId,
                            'quantity' => $quantity
                        ]);
                    }
                }
            }
            
            // Clear session cart after transfer
            session()->forget('cart');
            session()->forget('cart_count');
            
            // Update cart count from database
            $this->updateCartCount();
        }
    }

    /**
     * Update the cart count in session
     * 
     * @param bool $reset Whether to reset the count to 0
     */
    private function updateCartCount($reset = false)
    {
        if ($reset) {
            session(['cart_count' => 0]);
            return;
        }

        $cartCount = $this->getCartCount();
        session(['cart_count' => $cartCount]);
    }

    /**
     * Get the total number of items in the cart
     */
    private function getCartCount()
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->sum('quantity');
        }
        
        $sessionCart = session('cart', []);
        return array_sum($sessionCart);
    }
}
