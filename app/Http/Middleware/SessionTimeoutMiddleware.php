<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SessionTimeoutMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // If user is authenticated
        if (Auth::check()) {
            // Check if session has expired (30 minutes)
            if (Session::has('last_activity') && (time() - Session::get('last_activity') > 1800)) {
                Log::info("Session expired for user " . Auth::id() . ". Logging out.");
                
                // Clear all session data
                Session::flush();
                
                // Logout the user
                Auth::logout();
                
                // Redirect to login page with message
                return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
            }
            
            // Update last activity time
            Session::put('last_activity', time());
        }

        return $next($request);
    }
} 