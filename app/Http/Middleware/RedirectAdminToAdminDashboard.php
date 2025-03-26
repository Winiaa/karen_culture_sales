<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectAdminToAdminDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only redirect if the user is authenticated and an admin
        if (Auth::check() && Auth::user()->usertype === 'admin') {
            // Allow admins to access the admin section and logout route
            if (str_starts_with($request->path(), 'admin') || $request->routeIs('logout')) {
                return $next($request);
            }
            
            // Allow admins to see product and category details
            if (str_starts_with($request->path(), 'products/') || 
                str_starts_with($request->path(), 'categories/')) {
                return $next($request);
            }
            
            // Redirect admin to admin dashboard for other customer pages
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
} 