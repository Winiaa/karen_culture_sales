<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function count()
    {
        if (!Auth::check()) {
            return 0;
        }

        return Cart::where('user_id', Auth::id())->sum('quantity');
    }

    public function add(Product $product, $quantity = 1)
    {
        $cart = Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($cart) {
            $cart->update([
                'quantity' => $cart->quantity + $quantity
            ]);
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $quantity
            ]);
        }
    }

    public function update(Product $product, $quantity)
    {
        Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->update(['quantity' => $quantity]);
    }

    public function remove(Product $product)
    {
        Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->delete();
    }

    public function clear()
    {
        Cart::where('user_id', Auth::id())->delete();
    }

    public function items()
    {
        return Cart::where('user_id', Auth::id())
            ->with('product.category')
            ->get();
    }

    public function total()
    {
        $total = 0;
        foreach ($this->items() as $item) {
            $total += $item->quantity * $item->product->getFinalPriceAttribute();
        }
        return $total;
    }
} 