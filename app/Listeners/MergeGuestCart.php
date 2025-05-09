<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\Cart;

class MergeGuestCart
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // Get the logged-in user's ID
        $userId = $event->user->id;
        
        // Merge the guest cart with the user's cart
        Cart::mergeGuestCart($userId);
    }
} 