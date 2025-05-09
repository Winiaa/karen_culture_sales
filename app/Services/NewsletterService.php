<?php

namespace App\Services;

use App\Models\NewsletterSubscriber;
use App\Models\Product;
use App\Notifications\NewProductNotification;
use Illuminate\Support\Facades\Log;

class NewsletterService
{
    /**
     * Notify all active subscribers about a new product
     *
     * @param Product $product
     * @return void
     */
    public function notifyNewProduct(Product $product)
    {
        try {
            $subscribers = NewsletterSubscriber::where('is_active', true)->get();
            
            foreach ($subscribers as $subscriber) {
                $subscriber->notify(new NewProductNotification($product));
            }

            Log::info('New product notification sent', [
                'product_id' => $product->id,
                'product_title' => $product->title,
                'subscribers_count' => $subscribers->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send new product notification', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }
} 