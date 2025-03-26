<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        // Check if this is an admin trying to switch users
        $isSwitchingUser = request()->has('switch_to_user') && Auth::check() && Auth::user()->usertype === 'admin';
        
        return view('auth.login', [
            'isSwitchingUser' => $isSwitchingUser
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // If we have a logged-in admin trying to switch users, log them out first
        if (Auth::check() && Auth::user()->usertype === 'admin' && $request->has('switch_to_user')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        
        $request->authenticate();

        $request->session()->regenerate();

        // Redirect admin users to admin dashboard
        if (Auth::user()->usertype === 'admin') {
            return redirect()->intended(RouteServiceProvider::ADMIN_HOME);
        }
        
        // Redirect driver users to driver dashboard
        if (Auth::user()->usertype === 'driver') {
            return redirect()->intended('/driver/dashboard');
        }

        // Redirect regular users to homepage
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
