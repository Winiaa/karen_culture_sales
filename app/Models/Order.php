<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'Orders';

    protected $fillable = [
        'user_id',
        'total_amount',
        'payment_status',
        'order_status',
        'paid_at',
        'is_cancellable'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'is_cancellable' => 'boolean',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payment for the order.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get the delivery for the order.
     */
    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    /**
     * Check if the order can be cancelled.
     */
    public function canBeCancelled()
    {
        // Order must be in processing status
        if ($this->order_status !== 'processing') {
            return false;
        }
        
        // If cancellation is explicitly disabled
        if ($this->is_cancellable === false) {
            return false;
        }
        
        // For Stripe payments, check the 20-minute window
        if ($this->payment && $this->payment->payment_method === 'stripe' && $this->payment_status === 'completed') {
            // If paid_at is not set, we can't determine the window, so default to allow cancellation
            if (!$this->paid_at) {
                return true;
            }
            
            // Check if payment was made less than 20 minutes ago
            return $this->paid_at->diffInMinutes(now()) < 20;
        }
        
        // For cash on delivery
        if ($this->payment && $this->payment->payment_method === 'cash_on_delivery') {
            // Not cancellable if the delivery is marked as out for delivery
            if ($this->delivery && $this->delivery->isOutForDelivery()) {
                return false;
            }
        }
        
        // Default to true for other cases
        return true;
    }

    /**
     * Check if the order is within the Stripe cancellation window.
     */
    public function isWithinStripeCancellationWindow()
    {
        if (!$this->paid_at || $this->payment->payment_method !== 'stripe') {
            return false;
        }
        
        return $this->paid_at->diffInMinutes(now()) < 20;
    }

    /**
     * Calculate the remaining time (in minutes) for cancellation window.
     */
    public function getRemainingCancellationTime()
    {
        if (!$this->paid_at || $this->payment->payment_method !== 'stripe') {
            return 0;
        }
        
        $minutesPassed = $this->paid_at->diffInMinutes(now());
        return max(0, 20 - $minutesPassed);
    }

    /**
     * Check if the order is completed.
     */
    public function isCompleted()
    {
        return $this->order_status === 'delivered' && $this->payment_status === 'completed';
    }
}
