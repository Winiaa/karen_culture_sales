<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectDriverToDriverDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only redirect if the user is authenticated and a driver
        if (Auth::check() && Auth::user()->usertype === 'driver') {
            // Allow drivers to access the driver section and logout route
            if (str_starts_with($request->path(), 'driver') || $request->routeIs('logout')) {
                return $next($request);
            }
            
            // Allow drivers to access essential non-driver pages
            $allowedPaths = [
                'logout',
                '_debugbar',
                'livewire',
                'sanctum',
                'api'
            ];
            
            foreach ($allowedPaths as $path) {
                if (str_starts_with($request->path(), $path)) {
                    return $next($request);
                }
            }
            
            // Allow drivers to see only product and category details (read-only)
            // But not other customer pages like the main product listing
            if ((str_starts_with($request->path(), 'products/') || 
                str_starts_with($request->path(), 'categories/')) && !$request->routeIs('products.index') && !$request->routeIs('categories.index')) {
                return $next($request);
            }
            
            // Redirect driver to driver dashboard for all other pages, including homepage
            return redirect()->route('driver.dashboard')
                ->with('info', 'Please use the driver dashboard for your delivery operations.');
        }

        return $next($request);
    }
}
