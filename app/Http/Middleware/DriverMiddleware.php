<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DriverMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in
        if (!Auth::check()) {
            Log::info('DriverMiddleware: User not authenticated');
            return redirect()->route('login');
        }
        
        // Check if user is a driver
        if (Auth::user()->usertype !== 'driver') {
            Log::info('DriverMiddleware: User is not a driver, usertype: ' . Auth::user()->usertype);
            return redirect()->route('home')->with('error', 'You do not have permission to access this page.');
        }
        
        Log::info('DriverMiddleware: Driver authenticated successfully');
        return $next($request);
    }
}
