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
            \Log::info('Starting delivery confirmation process', ['delivery_id' => $delivery->id]);
            
            // Verify payment status for COD orders
            if ($delivery->order->payment && 
                $delivery->order->payment->payment_method === 'cash_on_delivery') {
                
                // Update payment status to completed for COD orders
                $delivery->order->payment->update([
                    'payment_status' => 'completed'
                ]);
                
                $delivery->order->update([
                    'payment_status' => 'completed'
                ]);
                
                \Log::info('Payment status updated for COD order', [
                    'delivery_id' => $delivery->id,
                    'order_id' => $delivery->order_id
                ]);
            }

            // Handle signature image storage
            $signatureData = $request->input('signature_data');
            $deliveryPhoto = null;
            
            if ($signatureData) {
                try {
                    // Remove the data:image/png;base64, part
                    $image = str_replace('data:image/png;base64,', '', $signatureData);
                    $image = str_replace(' ', '+', $image);
                    
                    // Decode base64 string to binary data
                    $imageBinary = base64_decode($image);
                    
                    // Generate unique filename
                    $filename = 'signature_' . uniqid() . '.png';
                    
                    // Store the file in the public storage
                    Storage::disk('public')->put('signatures/' . $filename, $imageBinary);
                    
                    $deliveryPhoto = 'signatures/' . $filename;
                    
                    \Log::info('Signature image saved successfully', [
                        'delivery_id' => $delivery->id,
                        'filename' => $filename
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error saving signature image', [
                        'delivery_id' => $delivery->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Use the markAsDelivered method from the Delivery model
            $delivery->markAsDelivered($deliveryPhoto, $request->input('delivery_notes'));

            \Log::info('Delivery status updated successfully', ['delivery_id' => $delivery->id]);

            // Send delivery receipt email for all orders
            try {
                \Log::info('Attempting to send delivery receipt email', [
                    'delivery_id' => $delivery->id,
                    'order_id' => $delivery->order_id,
                    'user_email' => $delivery->order->user->email
                ]);
                
                Mail::to($delivery->order->user->email)
                    ->send(new DeliveryReceiptMail($delivery));
                
                \Log::info('Delivery receipt email sent successfully', [
                    'delivery_id' => $delivery->id,
                    'order_id' => $delivery->order_id
                ]);
            } catch (\Exception $e) {
                \Log::error('Error sending delivery receipt email', [
                    'delivery_id' => $delivery->id,
                    'order_id' => $delivery->order_id,
                    'error' => $e->getMessage()
                ]);
            }

            return redirect()->route('driver.deliveries.show', $delivery)
                ->with('success', 'Delivery confirmed successfully');
        } catch (\Exception $e) {
            \Log::error('Error confirming delivery', [
                'delivery_id' => $delivery->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('driver.deliveries.show', $delivery)
                ->with('error', 'Error confirming delivery: ' . $e->getMessage());
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
}
