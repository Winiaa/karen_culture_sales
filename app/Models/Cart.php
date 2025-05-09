<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'session_id',
        'device_id'
    ];

    /**
     * Constants for validation and configuration
     */
    const MAX_QUANTITY_PER_ITEM = 99;
    const COOKIE_DURATION = 60 * 24 * 30; // 30 days
    const SESSION_COOKIE_NAME = 'cart_session_id';
    const DEVICE_COOKIE_NAME = 'cart_device_id';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cart) {
            if ($cart->quantity > self::MAX_QUANTITY_PER_ITEM) {
                throw new \InvalidArgumentException('Quantity exceeds maximum allowed.');
            }
        });

        static::updating(function ($cart) {
            if ($cart->quantity > self::MAX_QUANTITY_PER_ITEM) {
                throw new \InvalidArgumentException('Quantity exceeds maximum allowed.');
            }
        });
    }

    /**
     * Get the user that owns the cart item.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that owns the cart item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the total price for this cart item.
     */
    public function getTotalAttribute()
    {
        return $this->quantity * $this->product->price;
    }

    /**
     * Get the subtotal for this cart item.
     */
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->product->final_price;
    }

    /**
     * Get the total number of items in the cart for a user.
     */
    public static function count()
    {
        if (Auth::check()) {
            return self::where('user_id', Auth::id())->sum('quantity');
        }
        return self::where('session_id', self::getSessionId())->sum('quantity');
    }

    /**
     * Get or create a session ID for the cart
     */
    public static function getSessionId()
    {
        $sessionId = Cookie::get('cart_session_id');
        if (!$sessionId) {
            $sessionId = Str::random(40);
            Cookie::queue('cart_session_id', $sessionId, 60 * 24 * 30); // 30 days
        }
        return $sessionId;
    }

    /**
     * Get or create a device ID
     */
    public static function getDeviceId()
    {
        $deviceId = Cookie::get('cart_device_id');
        if (!$deviceId) {
            $deviceId = Str::random(40);
            Cookie::queue('cart_device_id', $deviceId, 60 * 24 * 30); // 30 days
        }
        return $deviceId;
    }

    /**
     * Add item to cart with validation
     */
    public static function addItem($productId, $quantity = 1)
    {
        // Validate product exists and is active
        $product = Product::where('status', 'active')->find($productId);
        if (!$product) {
            throw new \InvalidArgumentException('Product not found or inactive.');
        }

        // Validate quantity
        if ($quantity > self::MAX_QUANTITY_PER_ITEM) {
            throw new \InvalidArgumentException('Quantity exceeds maximum allowed.');
        }

        if ($quantity > $product->quantity) {
            throw new \InvalidArgumentException('Not enough stock available.');
        }

        $userId = Auth::id();
        $sessionId = Auth::check() ? null : self::getSessionId();
        $deviceId = Auth::check() ? null : self::getDeviceId();

        // Use database transaction for atomicity
        return DB::transaction(function() use ($userId, $productId, $quantity, $sessionId, $deviceId) {
            $cartItem = self::where(function($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->where('product_id', $productId)
            ->lockForUpdate()  // Prevent race conditions
            ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $quantity;
                if ($newQuantity > self::MAX_QUANTITY_PER_ITEM) {
                    throw new \InvalidArgumentException('Total quantity would exceed maximum allowed.');
                }
                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                $cartItem = self::create([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'session_id' => $sessionId,
                    'device_id' => $deviceId
                ]);
            }

            return $cartItem;
        });
    }

    /**
     * Merge guest cart with user cart after login with improved validation
     */
    public static function mergeGuestCart($userId)
    {
        return DB::transaction(function() use ($userId) {
            // First, handle session-based cart
            $sessionCart = session('cart', []);
            
            foreach ($sessionCart as $productId => $quantity) {
                try {
                    $product = Product::where('status', 'active')->find($productId);
                    if ($product) {
                        self::addItem($productId, $quantity);
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to merge cart item: {$e->getMessage()}", [
                        'user_id' => $userId,
                        'product_id' => $productId,
                        'quantity' => $quantity
                    ]);
                    continue;
                }
            }
            
            // Clear session cart
            session()->forget('cart');
            
            // Then, handle cookie-based cart items
            $sessionId = self::getSessionId();
            if ($sessionId) {
                $guestCartItems = self::where('session_id', $sessionId)
                    ->whereNull('user_id')
                    ->get();

                foreach ($guestCartItems as $item) {
                    try {
                        $existingItem = self::where('user_id', $userId)
                            ->where('product_id', $item->product_id)
                            ->lockForUpdate()
                            ->first();

                        if ($existingItem) {
                            $newQuantity = $existingItem->quantity + $item->quantity;
                            if ($newQuantity <= self::MAX_QUANTITY_PER_ITEM) {
                                $existingItem->quantity = $newQuantity;
                                $existingItem->save();
                            }
                            $item->delete();
                        } else {
                            $item->user_id = $userId;
                            $item->session_id = null;
                            $item->save();
                        }
                    } catch (\Exception $e) {
                        Log::warning("Failed to merge cart item: {$e->getMessage()}", [
                            'user_id' => $userId,
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity
                        ]);
                        continue;
                    }
                }
            }
        });
    }

    /**
     * Get cart items for current user or session
     */
    public static function getCartItems()
    {
        if (Auth::check()) {
            return self::where('user_id', Auth::id())
                ->with(['product' => function($query) {
                    $query->where('status', 'active');
                }])
                ->get();
        }
        return self::where('session_id', self::getSessionId())
            ->with(['product' => function($query) {
                $query->where('status', 'active');
            }])
            ->get();
    }
}
