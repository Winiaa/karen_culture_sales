<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home route with driver redirection
Route::middleware([\App\Http\Middleware\RedirectDriverToDriverDashboard::class])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Public product/category routes that drivers shouldn't access directly
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/new-arrivals', [ProductController::class, 'newArrivals'])->name('products.new-arrivals');
    Route::get('/products/best-sellers', [ProductController::class, 'bestSellers'])->name('products.best-sellers');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    
    // Static pages
    Route::get('/about', [PageController::class, 'about'])->name('about');
    Route::get('/contact', [PageController::class, 'contact'])->name('contact');
    Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
    Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
    Route::get('/faq', [PageController::class, 'faq'])->name('faq');
    Route::get('/artisans', [PageController::class, 'artisans'])->name('artisans');
    Route::get('/terms', [PageController::class, 'terms'])->name('terms');
});

// Product/category detail pages - these can be accessed by drivers
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

// Authentication routes
require __DIR__.'/auth.php';

// Order tracking routes - accessible without login
Route::get('/track', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/track', [TrackingController::class, 'track'])->name('tracking.track');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/shipping', [ProfileController::class, 'updateShipping'])->name('profile.update.shipping');
    Route::post('/profile/update-picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.update-picture');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Customer-only routes (redirect admins and drivers to their dashboards)
    Route::middleware([\App\Http\Middleware\RedirectAdminToAdminDashboard::class, \App\Http\Middleware\RedirectDriverToDriverDashboard::class])->group(function () {
        // Cart routes
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
        Route::put('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cart}', [CartController::class, 'remove'])->name('cart.remove');
        Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

        // Order routes
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        
        // Payment routes
        Route::get('/orders/{order}/payment/cash', [OrderController::class, 'cashPayment'])->name('orders.payment.cash');
        Route::get('/orders/{order}/payment/stripe', [StripePaymentController::class, 'showPaymentForm'])->name('payments.stripe');
        Route::post('/orders/{order}/payment/process', [StripePaymentController::class, 'processPayment'])->name('payments.process');
        Route::post('/orders/{order}/payment/confirm', [StripePaymentController::class, 'confirmPayment'])->name('payments.confirm');
        Route::get('/payments/callback', [StripePaymentController::class, 'handleCallback'])->name('payments.callback');

        // Review routes
        Route::post('/reviews/{product}', [ReviewController::class, 'store'])->name('reviews.store');
        Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
        
        // Handle GET requests for individual reviews by redirecting to the product page
        Route::get('/reviews/{review}', function(\App\Models\Review $review) {
            return redirect()->route('products.show', $review->product_id);
        })->name('reviews.show');
    });
});

// Include admin and driver routes
require __DIR__.'/admin.php';
require __DIR__.'/driver.php';
