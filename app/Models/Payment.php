<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method',
        'transaction_id',
        'payment_status'
    ];

    protected $table = 'Payments';

    /**
     * Get the order that owns the payment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if the payment is completed.
     */
    public function isCompleted()
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Check if the payment is pending.
     */
    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Get the status badge class for the payment.
     */
    public function getStatusBadgeClass()
    {
        return match($this->payment_status) {
            'completed' => 'bg-success',
            'pending' => 'bg-warning',
            default => 'bg-secondary',
        };
    }

    /**
     * Check if the payment is refunded.
     */
    public function isRefunded()
    {
        return $this->payment_status === 'refunded';
    }
}
