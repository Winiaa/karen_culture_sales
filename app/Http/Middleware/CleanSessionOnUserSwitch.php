<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CleanSessionOnUserSwitch
{
    public function handle(Request $request, Closure $next)
    {
        // If user is authenticated
        if (Auth::check()) {
            $currentUserType = Auth::user()->usertype;
            
            // Check if we have a previous user type in session
            if (Session::has('previous_user_type')) {
                $previousUserType = Session::get('previous_user_type');
                
                // If user type has changed, clear all session data
                if ($currentUserType !== $previousUserType) {
                    Log::info("User type changed from {$previousUserType} to {$currentUserType}. Clearing session.");
                    Session::flush();
                    
                    // Store new user type and activity time
                    Session::put('previous_user_type', $currentUserType);
                    Session::put('last_activity', time());
                    
                    // Redirect to appropriate dashboard based on user type
                    if ($currentUserType === 'admin') {
                        return redirect()->route('admin.dashboard');
                    } elseif ($currentUserType === 'driver') {
                        return redirect()->route('driver.dashboard');
                    }
                }
            } else {
                // First login - store current user type and activity time
                Session::put('previous_user_type', $currentUserType);
                Session::put('last_activity', time());
            }
            
            // Always update last activity time
            Session::put('last_activity', time());
        }

        return $next($request);
    }
} 