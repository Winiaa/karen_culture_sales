<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $table = 'drivers';

    protected $fillable = [
        'user_id',
        'phone_number',
        'vehicle_type',
        'license_number',
        'vehicle_plate',
        'is_active',
        'rating',
        'total_deliveries'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'decimal:2',
        'total_deliveries' => 'integer'
    ];

    /**
     * Get the user associated with the driver.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the deliveries associated with the driver.
     */
    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    /**
     * Get active deliveries for this driver.
     */
    public function activeDeliveries()
    {
        return $this->deliveries()
            ->where(function($query) {
                $query->where('delivery_status', 'assigned')
                      ->orWhere('delivery_status', 'picked_up')
                      ->orWhere('delivery_status', 'out_for_delivery');
            });
    }

    /**
     * Get completed deliveries for this driver.
     */
    public function completedDeliveries()
    {
        return $this->deliveries()
            ->where('delivery_status', 'delivered')
            ->whereNotNull('delivered_at');
    }

    /**
     * Check if driver is available for new deliveries.
     */
    public function isAvailable()
    {
        // First check if driver is active
        if (!$this->is_active) {
            return false;
        }
        
        // Then check if they have less than 5 active deliveries
        return $this->activeDeliveries()->count() < 5;
    }
}
