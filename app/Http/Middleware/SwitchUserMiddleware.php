<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SwitchUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is logged in and not an admin, redirect to home
        if (Auth::check() && Auth::user()->usertype !== 'admin' && !$request->has('switch_to_user')) {
            return redirect()->route('home');
        }

        // Allow the request to proceed
        return $next($request);
    }
} 