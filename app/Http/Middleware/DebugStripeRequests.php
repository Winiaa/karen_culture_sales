<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugStripeRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Log the incoming request details for Stripe-related routes
        if (strpos($request->path(), 'payment') !== false) {
            Log::info('Stripe route request', [
                'path' => $request->path(),
                'method' => $request->method(),
                'has_payment_method' => $request->has('payment_method_id'),
                'has_payment_intent' => $request->has('payment_intent_id'),
                'csrf_token_present' => $request->hasHeader('X-CSRF-TOKEN'),
            ]);
        }

        // Process the request
        $response = $next($request);

        // For JSON responses in Stripe routes, log the status
        if (strpos($request->path(), 'payment') !== false && 
            $response->headers->get('Content-Type') === 'application/json') {
            Log::info('Stripe route response', [
                'status' => $response->getStatusCode(),
                'content_type' => $response->headers->get('Content-Type'),
            ]);
        }

        return $response;
    }
} 