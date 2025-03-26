<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['orderItems.product', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['orderItems.product', 'payment', 'delivery']);
        return view('orders.show', compact('order'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:stripe,cash_on_delivery',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'recipient_address' => 'required|string',
            'save_shipping_info' => 'boolean'
        ]);

        // Save shipping information if requested
        if ($request->has('save_shipping_info') && $request->save_shipping_info) {
            auth()->user()->update([
                'default_recipient_name' => $request->recipient_name,
                'default_recipient_phone' => $request->recipient_phone,
                'default_shipping_address' => $request->recipient_address,
                'save_shipping_info' => true
            ]);
            
            \Log::info('Shipping information saved for user #' . auth()->id());
        }

        $cartItems = Cart::where('user_id', auth()->id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Your cart is empty.');
        }

        try {
            DB::beginTransaction();
            
            // Log the order creation attempt
            \Log::info('Creating order for user #' . auth()->id() . ' with ' . $cartItems->count() . ' items');

            // Calculate total (add validation to ensure positive amount)
            $total = $cartItems->sum(function($item) {
                return $item->quantity * $item->product->final_price;
            });
            
            if ($total <= 0) {
                \Log::error('Invalid order total: $' . $total);
                return back()->with('error', 'Invalid order total. Please contact support.');
            }
            
            \Log::info('Order total calculated: $' . $total);

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_amount' => $total,
                'payment_status' => 'pending',
                'order_status' => 'processing'
            ]);
            
            \Log::info('Order #' . $order->id . ' created');

            // Create order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->quantity * $item->product->final_price
                ]);

                // Update product quantity
                $item->product->decrement('quantity', $item->quantity);
            }
            
            \Log::info('Order items created for order #' . $order->id);

            // Create payment record
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending'
            ]);
            
            \Log::info('Payment record created for order #' . $order->id);
            
            // Create delivery record
            $order->delivery()->create([
                'user_id' => auth()->id(),
                'tracking_number' => 'TRK-' . strtoupper(substr(md5(uniqid()), 0, 10)),
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'recipient_address' => $request->recipient_address,
                'delivery_status' => 'pending'
            ]);
            
            \Log::info('Delivery record created for order #' . $order->id);
            
            // For Stripe payments, redirect to dedicated Stripe payment page
            if ($request->payment_method === 'stripe') {
                // Commit the transaction but don't clear the cart yet
                DB::commit();
                
                \Log::info('Redirecting to Stripe payment for order #' . $order->id);
                return redirect()->route('payments.stripe', $order);
            }
            
            // For cash on delivery, we can clear the cart now
            Cart::where('user_id', auth()->id())->delete();
            \Log::info('Cart cleared for user #' . auth()->id() . ' after cash on delivery order');
            
            DB::commit();

            // Send order confirmation email
            Mail::to(auth()->user()->email)->send(new OrderConfirmationMail($order));
            \Log::info('Order confirmation email sent for order #' . $order->id);

            $successMessage = 'Order placed successfully.';
            
            // Add note about shipping information being saved if applicable
            if ($request->has('save_shipping_info') && $request->save_shipping_info) {
                $successMessage .= ' Your shipping information has been saved for future orders.';
            }

            return redirect()->route('orders.show', $order)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order creation error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Process cash payment.
     */
    public function cashPayment(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->payment->update([
            'payment_status' => 'pending',
            'payment_method' => 'cash_on_delivery'
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order confirmed for cash on delivery.');
    }

    /**
     * Cancel the specified order.
     */
    public function cancel(Order $order)
    {
        // Check order ownership
        if ($order->user_id !== auth()->id()) {
            abort(403, 'You do not own this order.');
        }
        
        // Check if order can be cancelled
        if (!$order->canBeCancelled()) {
            // Provide specific reasons why the order can't be cancelled
            if ($order->order_status !== 'processing') {
                return back()->with('error', 'This order cannot be cancelled because it is already ' . $order->order_status . '.');
            }
            
            if ($order->payment && $order->payment->payment_method === 'stripe' && $order->paid_at) {
                if ($order->paid_at->diffInMinutes(now()) >= 20) {
                    return back()->with('error', 'Stripe orders can only be cancelled within 20 minutes of payment. This time has elapsed.');
                }
            }
            
            if ($order->payment && $order->payment->payment_method === 'cash_on_delivery' && 
                $order->delivery && $order->delivery->delivery_status === 'out_for_delivery') {
                return back()->with('error', 'Cash on delivery orders cannot be cancelled once they are out for delivery.');
            }
            
            return back()->with('error', 'This order cannot be cancelled.');
        }

        try {
            DB::beginTransaction();

            // Update order status
            $order->update([
                'order_status' => 'cancelled'
            ]);

            // Restore product quantities
            foreach ($order->orderItems as $item) {
                $item->product->increment('quantity', $item->quantity);
            }

            // Update payment status if necessary
            if ($order->payment) {
                $order->payment->update([
                    'payment_status' => 'failed'
                ]);
            }

            DB::commit();

            return back()->with('success', 'Order cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to cancel order: ' . $e->getMessage());
            return back()->with('error', 'Unable to cancel order. Please try again.');
        }
    }

    /**
     * Confirm delivery of an order by the customer.
     */
    public function confirmDelivery(Request $request, Order $order)
    {
        // Check order ownership
        if ($order->user_id !== auth()->id()) {
            abort(403, 'You do not own this order.');
        }
        
        // Check if order is delivered
        if (!$order->delivery || $order->delivery->delivery_status !== 'delivered') {
            return back()->with('error', 'This order is not marked as delivered yet.');
        }
        
        // Check if delivery is already confirmed
        if ($order->delivery->is_confirmed_by_customer) {
            return back()->with('info', 'Delivery has already been confirmed.');
        }
        
        try {
            DB::beginTransaction();
            
            // Mark delivery as confirmed by customer
            $order->delivery->markAsConfirmedByCustomer();
            
            // For cash on delivery orders, update the payment status
            if ($order->payment && $order->payment->payment_method === 'cash_on_delivery') {
                $order->payment->update(['payment_status' => 'completed']);
                $order->update(['payment_status' => 'completed']);
            }
            
            DB::commit();
            
            return back()->with('success', 'Delivery confirmed successfully. Thank you!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to confirm delivery: ' . $e->getMessage());
            return back()->with('error', 'Unable to confirm delivery. Please try again.');
        }
    }
}
