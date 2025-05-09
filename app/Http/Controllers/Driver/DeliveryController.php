<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Mail\DeliveryReceiptMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    /**
     * Display a listing of all deliveries for the driver.
     */
    public function index(Request $request): View
    {
        $driver = Auth::user()->driver;
        $query = $driver->deliveries()->with('order');
        
        // Apply filters
        $query = $this->applyFilters($query, $request);
        
        $deliveries = $query->latest()->paginate(10);
        
        return view('driver.deliveries.index', [
            'deliveries' => $deliveries,
            'title' => 'All Deliveries'
        ]);
    }
    
    /**
     * Display a listing of assigned deliveries for the driver.
     */
    public function assigned(Request $request): View
    {
        $driver = Auth::user()->driver;
        $query = $driver->activeDeliveries()->with('order');
        
        // Apply filters
        $query = $this->applyFilters($query, $request);
        
        $deliveries = $query->latest()->paginate(10);
        
        return view('driver.deliveries.index', [
            'deliveries' => $deliveries,
            'title' => 'Assigned Deliveries'
        ]);
    }
    
    /**
     * Display a listing of completed deliveries for the driver.
     */
    public function completed(Request $request): View
    {
        $driver = Auth::user()->driver;
        $query = $driver->completedDeliveries()->with('order');
        
        // Apply filters
        $query = $this->applyFilters($query, $request);
        
        $deliveries = $query->latest()->paginate(10);
        
        return view('driver.deliveries.index', [
            'deliveries' => $deliveries,
            'title' => 'Completed Deliveries'
        ]);
    }
    
    /**
     * Display the specified delivery.
     */
    public function show(Delivery $delivery): View
    {
        // Ensure the delivery belongs to the authenticated driver
        $this->authorizeDelivery($delivery);
        
        // Load the order and its items
        $delivery->load(['order.orderItems.product', 'order.user']);
        
        return view('driver.deliveries.show', [
            'delivery' => $delivery
        ]);
    }
    
    /**
     * Mark a delivery as picked up.
     */
    public function pickup(Request $request, Delivery $delivery)
    {
        $this->authorizeDelivery($delivery);
        
        $delivery->markAsPickedUp();
        
        return redirect()->route('driver.deliveries.show', $delivery)
            ->with('success', 'Delivery marked as picked up successfully.');
    }
    
    /**
     * Mark a delivery as out for delivery.
     */
    public function outForDelivery(Request $request, Delivery $delivery)
    {
        $this->authorizeDelivery($delivery);
        
        $delivery->markAsOutForDelivery();
        
        // Update order status to shipped
        $delivery->order->update([
            'order_status' => 'shipped'
        ]);
        
        return redirect()->route('driver.deliveries.show', $delivery)
            ->with('success', 'Delivery marked as out for delivery successfully.');
    }
    
    /**
     * Mark a delivery as delivered.
     */
    public function deliver(Request $request, Delivery $delivery)
    {
        try {
            Log::info('Starting delivery confirmation process', ['delivery_id' => $delivery->id]);
            
            // Verify this is the assigned driver
            if ($delivery->driver_id !== Auth::user()->driver->id) {
                throw new \Exception('Unauthorized delivery confirmation attempt.');
            }

            // Handle signature image storage
            $signatureData = $request->input('signature_data');
            $deliveryPhoto = null;
            
            if ($signatureData) {
                // Remove the "data:image/png;base64," part
                $signatureData = explode(',', $signatureData)[1];
                
                // Decode the base64 data
                $decodedData = base64_decode($signatureData);
                
                if ($decodedData === false) {
                    throw new \Exception('Invalid signature data provided.');
                }
                
                // Generate a unique filename
                $filename = 'signature_' . uniqid() . '.png';
                
                // Store the file
                Storage::disk('public')->put('signatures/' . $filename, $decodedData);
                
                $deliveryPhoto = 'signatures/' . $filename;
                
                Log::info('Signature stored successfully', [
                    'delivery_id' => $delivery->id,
                    'filename' => $filename
                ]);
            } else {
                throw new \Exception('Signature is required for delivery confirmation.');
            }

            DB::transaction(function() use ($delivery, $deliveryPhoto, $request) {
                // Mark delivery as delivered
                $delivery->update([
                    'delivery_status' => 'delivered',
                    'delivered_at' => now(),
                    'delivery_photo' => $deliveryPhoto,
                    'delivery_notes' => $request->input('delivery_notes')
                ]);

                // Update order status
                $delivery->order->update([
                    'order_status' => 'delivered'
                ]);
            });

            Log::info('Delivery confirmation completed successfully', ['delivery_id' => $delivery->id]);

            // Send delivery receipt email
            try {
                Mail::to($delivery->order->user->email)
                    ->send(new DeliveryReceiptMail($delivery));
                
                Log::info('Delivery receipt email sent', [
                    'delivery_id' => $delivery->id,
                    'user_email' => $delivery->order->user->email
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send delivery receipt email', [
                    'delivery_id' => $delivery->id,
                    'error' => $e->getMessage()
                ]);
            }

            return redirect()->route('driver.deliveries.show', $delivery)
                ->with('success', 'Delivery confirmed successfully.');

        } catch (\Exception $e) {
            Log::error('Delivery confirmation failed', [
                'delivery_id' => $delivery->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error confirming delivery: ' . $e->getMessage());
        }
    }
    
    /**
     * Mark a delivery as failed.
     */
    public function fail(Request $request, Delivery $delivery)
    {
        $this->authorizeDelivery($delivery);
        
        $request->validate([
            'delivery_notes' => 'required|string|max:500',
        ]);
        
        $delivery->markAsFailed($request->delivery_notes);
        
        return redirect()->route('driver.deliveries.show', $delivery)
            ->with('success', 'Delivery marked as failed.');
    }
    
    /**
     * Ensure the delivery belongs to the authenticated driver.
     */
    private function authorizeDelivery(Delivery $delivery)
    {
        $driver = Auth::user()->driver;
        
        if ($delivery->driver_id !== $driver->id) {
            abort(403, 'You are not authorized to access this delivery.');
        }
    }
    
    /**
     * Apply filters to the deliveries query based on request parameters.
     */
    private function applyFilters($query, Request $request)
    {
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('order', function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%");
            });
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $query->where('delivery_status', $request->status);
        }
        
        // Apply date filter
        if ($request->filled('date')) {
            $date = $request->date;
            
            switch ($date) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
            }
        }
        
        return $query;
    }

    /**
     * Update delivery payment status
     */
    public function updatePayment(Request $request, Delivery $delivery)
    {
        try {
            DB::beginTransaction();

            // Verify this delivery belongs to the driver
            if ($delivery->driver_id !== Auth::user()->driver->id) {
                abort(403, 'Unauthorized action.');
            }

            // Verify this is a COD delivery
            if (!$delivery->order->payment || $delivery->order->payment->payment_method !== 'cash_on_delivery') {
                return back()->with('error', 'This is not a COD delivery.');
            }

            // Different validation based on payment type
            if ($request->has('cash_collected')) {
                // Cash payment
                $request->validate([
                    'cash_collected' => 'required|accepted',
                    'payment_notes' => 'nullable|string|max:500'
                ]);

                // Update delivery payment details
                $delivery->update([
                    'payment_status' => 'received',
                    'payment_received_at' => now(),
                    'payment_notes' => $request->payment_notes ?? 'Cash payment collected'
                ]);
            } else {
                // Bank transfer
                $request->validate([
                    'transfer_proof' => 'required|image|max:2048', // Max 2MB
                    'payment_notes' => 'nullable|string|max:500'
                ]);

                // Store the transfer proof
                $path = $request->file('transfer_proof')->store('transfer-proofs', 'public');

                // Update delivery payment details
                $delivery->update([
                    'transfer_proof' => $path,
                    'payment_status' => 'received',
                    'payment_received_at' => now(),
                    'payment_notes' => $request->payment_notes
                ]);
            }

            // Update payment record
            $delivery->order->payment->update([
                'payment_status' => 'completed',
                'paid_at' => now()
            ]);

            // Update order payment status
            $delivery->order->update([
                'payment_status' => 'completed',
                'paid_at' => now()
            ]);

            DB::commit();

            Log::info('Payment status updated successfully', [
                'delivery_id' => $delivery->id,
                'order_id' => $delivery->order->id,
                'payment_type' => $request->has('cash_collected') ? 'cash' : 'transfer',
                'payment_status' => 'completed'
            ]);

            return back()->with('success', 'Payment received and recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update payment status', [
                'delivery_id' => $delivery->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to update payment status. Please try again.');
        }
    }

    /**
     * Retry a failed delivery.
     */
    public function retry(Delivery $delivery)
    {
        try {
            // Check if the delivery belongs to the current driver
            if ($delivery->driver_id !== auth()->user()->driver->id) {
                return redirect()->back()->with('error', 'You are not authorized to retry this delivery.');
            }

            // Retry the failed delivery
            $delivery->retryFailedDelivery();

            return redirect()->back()->with('success', 'Delivery has been marked for retry. You can now attempt delivery again.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
