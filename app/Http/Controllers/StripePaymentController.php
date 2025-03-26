<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Exception\ApiErrorException;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;

class StripePaymentController extends Controller
{
    /**
     * Display the Stripe payment form.
     */
    public function showPaymentForm(Order $order)
    {
        // Ensure the user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Load the order with its relationships
        $order->load(['orderItems.product', 'payment']);

        // Ensure the order is in a payable state
        if ($order->payment_status === 'completed') {
            return redirect()->route('orders.show', $order)
                ->with('info', 'This order has already been paid.');
        }

        // Check if the payment method is Stripe
        if ($order->payment->payment_method !== 'stripe') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'This order is not set for credit card payment.');
        }

        // Get Stripe publishable key from config
        $stripeKey = config('services.stripe.key');

        // Check if Stripe is properly configured
        if (empty($stripeKey)) {
            Log::error('Stripe key not found in configuration');
            return redirect()->route('orders.show', $order)
                ->with('error', 'Payment system is not properly configured. Please contact support.');
        }

        return view('payments.stripe', compact('order', 'stripeKey'));
    }

    /**
     * Process the Stripe payment.
     */
    public function processPayment(Request $request, Order $order)
    {
        // Validate ownership
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Validate request data
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        // Load the order with its relationships
        $order->load(['payment']);

        // Check if the order has already been paid
        if ($order->payment_status === 'completed') {
            return response()->json([
                'success' => true,
                'message' => 'This order has already been paid.',
                'redirect' => route('orders.show', $order)
            ]);
        }

        // Get Stripe secret key
        $stripeSecret = config('services.stripe.secret');

        // Log the key (partially redacted for security)
        if (!empty($stripeSecret)) {
            $keyLength = strlen($stripeSecret);
            $redactedKey = substr($stripeSecret, 0, 4) . '...' . substr($stripeSecret, $keyLength - 4, 4);
            Log::info('Using Stripe key starting with: ' . $redactedKey . ' (length: ' . $keyLength . ')');
        } else {
            Log::error('Stripe secret key not found in configuration');
            return response()->json([
                'error' => 'Payment system is not properly configured. Please contact support.'
            ], 500);
        }

        // Log the start of payment processing
        Log::info('Starting Stripe payment process for order #' . $order->id, [
            'order_total' => $order->total_amount,
            'payment_method_id' => substr($request->payment_method_id, 0, 8) . '...' // Log partial ID for privacy
        ]);

        try {
            // Set Stripe API key
            Stripe::setApiKey($stripeSecret);
            
            // Double-check that we can actually call Stripe API
            try {
                // Make a simple call to verify connectivity
                $balance = \Stripe\Balance::retrieve();
                Log::info('Stripe API connection successful');
            } catch (\Exception $e) {
                Log::error('Stripe API connectivity test failed: ' . $e->getMessage());
            }

            // Calculate the payment amount and ensure it's valid
            $amount = (int)round($order->total_amount * 100); // Amount in cents
            if ($amount <= 0) {
                throw new \Exception('Invalid payment amount: ' . $amount);
            }
            
            Log::info('Creating payment intent', [
                'amount' => $amount,
                'currency' => 'usd',
                'payment_method' => substr($request->payment_method_id, 0, 8) . '...'
            ]);

            // Create a payment intent
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'payment_method' => $request->payment_method_id,
                'confirm' => true,
                'description' => 'Order #' . $order->id,
                'metadata' => [
                    'order_id' => $order->id,
                    'customer_name' => auth()->user()->name,
                    'customer_email' => auth()->user()->email,
                ],
                'receipt_email' => auth()->user()->email,
                'return_url' => route('payments.callback'),
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'always'
                ],
            ]);

            // Log payment intent details
            Log::info('Payment intent created', [
                'payment_intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'requires_action' => isset($paymentIntent->next_action),
            ]);

            // Handle the payment result
            if ($paymentIntent->status === 'succeeded') {
                // Update the payment and order
                $this->processSuccessfulPayment($order, $paymentIntent);

                // Clear the cart
                Cart::where('user_id', auth()->id())->delete();

                // Store shipping info message in session if applicable
                if ($order->user->save_shipping_info) {
                    session()->flash('shipping_saved', true);
                }

                return response()->json([
                    'success' => true,
                    'redirect' => route('orders.show', $order) . '?payment_success=true',
                ]);
            } elseif ($paymentIntent->status === 'requires_action' && 
                      $paymentIntent->next_action && 
                      $paymentIntent->next_action->type === 'use_stripe_sdk') {
                // Handle payment that requires additional action
                Log::info('Payment requires additional action', [
                    'action_type' => $paymentIntent->next_action->type,
                    'payment_intent_id' => $paymentIntent->id
                ]);
                
                return response()->json([
                    'requires_action' => true,
                    'payment_intent_client_secret' => $paymentIntent->client_secret,
                    'order_id' => $order->id
                ]);
            } else {
                // Handle other payment statuses
                Log::warning('Payment unsuccessful. Status: ' . $paymentIntent->status, [
                    'order_id' => $order->id,
                    'payment_intent_id' => $paymentIntent->id
                ]);
                
                return response()->json([
                    'error' => 'Payment could not be processed. Status: ' . $paymentIntent->status
                ], 400);
            }
        } catch (ApiErrorException $e) {
            // Log and handle Stripe API errors
            Log::error('Stripe API Error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'error_type' => get_class($e),
                'error_code' => $e->getStripeCode(),
                'decline_code' => method_exists($e, 'getDeclineCode') ? $e->getDeclineCode() : null,
                'http_status' => method_exists($e, 'getHttpStatus') ? $e->getHttpStatus() : null
            ]);

            // Determine a user-friendly error message
            $errorMessage = $this->getPaymentErrorMessage($e);

            return response()->json([
                'error' => $errorMessage
            ], 400);
        } catch (\Exception $e) {
            // Log general errors
            Log::error('Payment Error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm the payment after additional authentication steps.
     */
    public function confirmPayment(Request $request, Order $order)
    {
        // Validate ownership
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Validate request data
        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        // Get Stripe secret key
        $stripeSecret = config('services.stripe.secret');

        if (empty($stripeSecret)) {
            Log::error('Stripe secret key not found in configuration during payment confirmation');
            return response()->json([
                'error' => 'Payment system is not properly configured. Please contact support.'
            ], 500);
        }

        Log::info('Confirming payment for order #' . $order->id, [
            'payment_intent_id' => substr($request->payment_intent_id, 0, 8) . '...',
        ]);

        try {
            // Set Stripe API key
            Stripe::setApiKey($stripeSecret);

            // Retrieve the payment intent
            $paymentIntent = \Stripe\PaymentIntent::retrieve($request->payment_intent_id);
            
            Log::info('Payment intent retrieved', [
                'payment_intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
            ]);

            // Check payment intent status
            if ($paymentIntent->status === 'succeeded') {
                // Process successful payment
                $this->processSuccessfulPayment($order, $paymentIntent);

                // Clear the cart
                Cart::where('user_id', auth()->id())->delete();

                return response()->json([
                    'success' => true,
                    'redirect' => route('orders.show', $order) . '?payment_success=true'
                ]);
            } else {
                // Handle other statuses
                Log::warning('Payment confirmation failed. Status: ' . $paymentIntent->status, [
                    'order_id' => $order->id,
                    'payment_intent_id' => $paymentIntent->id,
                ]);

                return response()->json([
                    'error' => 'Payment could not be completed. Please try again or choose a different payment method.',
                    'status' => $paymentIntent->status
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Payment confirmation error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'payment_intent_id' => $request->payment_intent_id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'An error occurred during payment confirmation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process a successful payment.
     */
    private function processSuccessfulPayment(Order $order, $paymentIntent)
    {
        // Update the payment record
        $order->payment->update([
            'transaction_id' => $paymentIntent->id,
            'payment_status' => 'completed',
        ]);

        // Update the order status
        $order->update([
            'payment_status' => 'completed',
            'paid_at' => now() // Set the paid_at timestamp
        ]);

        // Send order confirmation email
        Mail::to($order->user->email)->queue(new OrderConfirmationMail($order));
        \Log::info('Order confirmation email queued for order #' . $order->id);

        Log::info('Payment successful for order #' . $order->id, [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount / 100,
            'paid_at' => now()->toDateTimeString()
        ]);
    }

    /**
     * Get a user-friendly error message based on the Stripe exception.
     */
    private function getPaymentErrorMessage($exception)
    {
        $errorCode = $exception->getStripeCode();
        
        switch ($errorCode) {
            case 'card_declined':
                return 'Your card was declined. Please try another card.';
                
            case 'expired_card':
                return 'Your card has expired. Please try another card.';
                
            case 'incorrect_cvc':
                return 'The security code (CVC) is incorrect. Please check and try again.';
                
            case 'processing_error':
                return 'An error occurred while processing your card. Please try again.';
                
            case 'incorrect_number':
                return 'The card number is incorrect. Please check and try again.';
                
            case 'insufficient_funds':
                return 'Your card has insufficient funds. Please try another card.';
                
            default:
                // For development, you might want to show the actual error
                if (config('app.debug')) {
                    return 'Payment error: ' . $exception->getMessage();
                }
                
                return 'There was a problem with your payment. Please try again or use a different card.';
        }
    }

    /**
     * Handle the callback from Stripe after a redirect-based payment flow.
     */
    public function handleCallback(Request $request)
    {
        Log::info('Payment callback received', [
            'query_params' => $request->all(),
        ]);

        // Get payment_intent from the URL parameters
        $paymentIntentId = $request->payment_intent;
        
        if (!$paymentIntentId) {
            Log::error('No payment intent ID in callback');
            return redirect()->route('orders.index')
                ->with('error', 'We could not process your payment. Please try again.');
        }

        // Get Stripe secret
        $stripeSecret = config('services.stripe.secret');
        if (empty($stripeSecret)) {
            Log::error('Stripe secret key missing in callback');
            return redirect()->route('orders.index')
                ->with('error', 'Payment system configuration error. Please contact support.');
        }

        try {
            // Set Stripe API key
            Stripe::setApiKey($stripeSecret);

            // Retrieve the payment intent
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
            
            Log::info('Payment intent retrieved in callback', [
                'payment_intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'metadata' => $paymentIntent->metadata->toArray(),
            ]);

            // Find the order using metadata
            $orderId = $paymentIntent->metadata->order_id ?? null;
            
            if (!$orderId) {
                Log::error('No order ID in payment intent metadata');
                return redirect()->route('orders.index')
                    ->with('error', 'Could not match your payment to an order. Please contact support.');
            }

            $order = Order::find($orderId);
            
            if (!$order) {
                Log::error('Order not found for ID: ' . $orderId);
                return redirect()->route('orders.index')
                    ->with('error', 'Order not found. Please contact support.');
            }

            // Check payment status
            if ($paymentIntent->status === 'succeeded') {
                // Process successful payment
                $this->processSuccessfulPayment($order, $paymentIntent);

                // Clear the cart
                Cart::where('user_id', auth()->id())->delete();

                // Check if shipping information was saved for this order
                $successMessage = 'Your payment was successful!';
                if ($order->user->save_shipping_info) {
                    $successMessage .= ' Your shipping information has been saved for future orders.';
                }

                return redirect()->route('orders.show', $order)
                    ->with('success', $successMessage);
                    
            } else if ($paymentIntent->status === 'requires_payment_method') {
                // Payment failed or was declined
                Log::warning('Payment failed in callback flow', [
                    'order_id' => $order->id,
                    'status' => $paymentIntent->status,
                ]);
                
                return redirect()->route('payments.stripe', $order)
                    ->with('error', 'Your payment was not successful. Please try again with a different payment method.');
                    
            } else {
                // Other status
                Log::warning('Payment in uncertain state', [
                    'order_id' => $order->id,
                    'status' => $paymentIntent->status,
                ]);
                
                return redirect()->route('orders.show', $order)
                    ->with('info', 'Your payment is being processed. We will update your order status shortly.');
            }
            
        } catch (\Exception $e) {
            Log::error('Error in payment callback: ' . $e->getMessage(), [
                'payment_intent_id' => $paymentIntentId,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return redirect()->route('orders.index')
                ->with('error', 'An error occurred while processing your payment. Please contact support.');
        }
    }
} 