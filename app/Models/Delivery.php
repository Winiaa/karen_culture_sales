<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Delivery extends Model
{
    use HasFactory;

    protected $table = 'deliveries';

    protected $fillable = [
        'order_id',
        'user_id',
        'driver_id',
        'tracking_number',
        'estimated_delivery_date',
        'delivered_at',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'delivery_status',
        'is_confirmed_by_customer',
        'confirmed_at',
        'delivery_photo',
        'delivery_notes',
        'transfer_proof',
        'payment_status',
        'payment_received_at',
        'payment_notes'
    ];

    protected $casts = [
        'estimated_delivery_date' => 'date',
        'delivered_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'is_confirmed_by_customer' => 'boolean',
        'payment_received_at' => 'datetime'
    ];

    /**
     * Get the order that owns the delivery.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user that owns the delivery.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the driver that handles the delivery.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Check if the delivery is completed.
     */
    public function isDelivered()
    {
        return $this->delivery_status === 'delivered';
    }

    /**
     * Check if the delivery is out for delivery.
     */
    public function isOutForDelivery()
    {
        return $this->delivery_status === 'out_for_delivery';
    }

    /**
     * Check if the delivery is confirmed by customer.
     */
    public function isConfirmedByCustomer()
    {
        return $this->is_confirmed_by_customer;
    }

    /**
     * Check if the delivery is assigned to a driver.
     */
    public function isAssigned()
    {
        return $this->delivery_status === 'assigned';
    }

    /**
     * Check if the delivery is picked up.
     */
    public function isPickedUp()
    {
        return $this->delivery_status === 'picked_up';
    }

    /**
     * Check if the delivery has failed.
     */
    public function hasFailed()
    {
        return $this->delivery_status === 'failed';
    }

    /**
     * Assign a driver to this delivery.
     */
    public function assignDriver(Driver $driver)
    {
        try {
            // Log the attempt
            \Log::info('Starting driver assignment process', [
                'delivery_id' => $this->id,
                'driver_id' => $driver->id,
                'current_status' => $this->delivery_status
            ]);

            // Validate driver status
            if (!$driver->is_active) {
                throw new \Exception('Selected driver is not active.');
            }

            if (!$driver->isAvailable()) {
                throw new \Exception('Selected driver has reached the maximum number of active deliveries.');
            }

            // Check if driver is already assigned
            if ($this->driver_id === $driver->id) {
                throw new \Exception('This driver is already assigned to this delivery.');
            }

            // Start transaction
            DB::beginTransaction();

            try {
                // Update delivery record
                $updated = $this->forceFill([
                    'driver_id' => $driver->id,
                    'delivery_status' => 'assigned'
                ])->save();

                if (!$updated) {
                    throw new \Exception('Failed to update delivery record.');
                }

                // Update order status if needed
                if ($this->order && $this->order->order_status === 'processing') {
                    $orderUpdated = $this->order->update(['order_status' => 'shipped']);
                    if (!$orderUpdated) {
                        throw new \Exception('Failed to update order status.');
                    }
                }

                // Commit transaction
                DB::commit();

                // Log successful assignment
                \Log::info('Driver assigned successfully', [
                    'delivery_id' => $this->id,
                    'driver_id' => $driver->id,
                    'order_id' => $this->order_id
                ]);

                // Refresh the model
                $this->refresh();

                return $this;

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            // Log the error with detailed information
            \Log::error('Error assigning driver to delivery:', [
                'delivery_id' => $this->id,
                'driver_id' => $driver->id,
                'order_id' => $this->order_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Mark as picked up.
     */
    public function markAsPickedUp()
    {
        $this->update([
            'delivery_status' => 'picked_up'
        ]);
        
        return $this;
    }

    /**
     * Mark as out for delivery.
     */
    public function markAsOutForDelivery()
    {
        $this->update([
            'delivery_status' => 'out_for_delivery'
        ]);
        
        // Update the order to be non-cancellable for COD orders
        if ($this->order->payment && $this->order->payment->payment_method === 'cash_on_delivery') {
            $this->order->update(['is_cancellable' => false]);
        }
        
        return $this;
    }

    /**
     * Mark as delivered.
     */
    public function markAsDelivered($photo = null, $notes = null)
    {
        Log::info('Marking delivery #' . $this->id . ' as delivered');
        
        try {
            // Update delivery status and details
            $this->update([
                'delivery_status' => 'delivered',
                'delivered_at' => now(),
                'delivery_photo' => $photo,
                'delivery_notes' => $notes
            ]);
            
            Log::info('Updated delivery status to delivered');
            
            // Update the order status
            $this->order->update(['order_status' => 'delivered']);
            Log::info('Updated order status to delivered');
            
            // If this is a cash on delivery order, mark the payment as completed immediately
            if ($this->order->payment && $this->order->payment->payment_method === 'cash_on_delivery') {
                Log::info('This is a cash on delivery order - updating payment status to completed');
                
                // Update payment status
                $paymentUpdated = $this->order->payment->update(['payment_status' => 'completed']);
                Log::info('Payment status update result: ' . ($paymentUpdated ? 'success' : 'failed'));
                
                // Update order payment status
                $orderUpdated = $this->order->update(['payment_status' => 'completed']);
                Log::info('Order payment status update result: ' . ($orderUpdated ? 'success' : 'failed'));
            } else {
                Log::info('Not a cash on delivery order or no payment found');
            }
            
            // Increment the driver's total deliveries count
            if ($this->driver) {
                $this->driver->increment('total_deliveries');
                Log::info('Incremented driver total deliveries');
            }
            
            Log::info('Delivery marked as delivered successfully');
            
        } catch (\Exception $e) {
            Log::error('Error marking delivery as delivered: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
        
        return $this;
    }

    /**
     * Mark as confirmed by customer.
     */
    public function markAsConfirmedByCustomer()
    {
        $this->update([
            'is_confirmed_by_customer' => true,
            'confirmed_at' => now()
        ]);
        
        // If this is a COD order, mark the payment as completed
        if ($this->order->payment && $this->order->payment->payment_method === 'cash_on_delivery') {
            $this->order->payment->update(['payment_status' => 'completed']);
            $this->order->update(['payment_status' => 'completed']);
        }
        
        // Increment driver's total deliveries
        if ($this->driver) {
            $this->driver->increment('total_deliveries');
        }
        
        return $this;
    }

    /**
     * Mark as failed.
     */
    public function markAsFailed($notes = null)
    {
        $this->update([
            'delivery_status' => 'failed',
            'delivery_notes' => $notes
        ]);
        
        return $this;
    }

    /**
     * Retry a failed delivery.
     */
    public function retryFailedDelivery()
    {
        if ($this->delivery_status !== 'failed') {
            throw new \Exception('Only failed deliveries can be retried.');
        }

        $this->update([
            'delivery_status' => 'out_for_delivery',
            'delivery_notes' => null // Clear previous failure notes
        ]);
        
        return $this;
    }

    /**
     * Confirm delivery by customer.
     */
    public function confirmByCustomer()
    {
        $this->update([
            'is_confirmed_by_customer' => true,
            'confirmed_at' => now()
        ]);
        
        return $this;
    }
}
