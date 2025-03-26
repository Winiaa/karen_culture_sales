<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DriverController as AdminDriverController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Driver\DashboardController as DriverDashboardController;
use App\Http\Controllers\Driver\DeliveryController as DriverDeliveryController;
use App\Http\Controllers\Driver\ProfileController as DriverProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

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

// Fallback GET route for logout that redirects to the home page
Route::get('/logout', function() {
    return redirect()->route('home');
})->name('logout.get');

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
        Route::get('/cart/{cart}', function() {
            return redirect()->route('cart.index');
        });

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
        Route::get('/payment/callback', [StripePaymentController::class, 'handleCallback'])->name('payments.callback');

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

// Admin routes
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // Admin Dashboard
        Route::get('/dashboard', [AdminOrderController::class, 'dashboard'])->name('dashboard');
        
        // Admin Profile
        Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/update-picture', [AdminProfileController::class, 'updateProfilePicture'])->name('profile.update-picture');
        
        // Sales Report
        Route::get('/reports/sales', [AdminOrderController::class, 'salesReport'])->name('reports.sales');
        Route::get('/reports/sales/export', [AdminOrderController::class, 'exportSalesReport'])->name('reports.sales.export');
        
        // Products
        Route::resource('products', AdminProductController::class);
        Route::post('/products/{product}/restore', [AdminProductController::class, 'restore'])->name('products.restore');
        Route::delete('/products/reviews/{review}', [AdminProductController::class, 'deleteReview'])->name('products.delete-review');
        
        // Categories
        Route::resource('categories', AdminCategoryController::class);
        
        // Orders
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        Route::put('/orders/{order}/delivery', [AdminOrderController::class, 'updateDelivery'])->name('orders.delivery.update');
        Route::post('/orders/{order}/assign-driver', [AdminOrderController::class, 'assignDriver'])->name('orders.assign-driver');
        Route::put('/orders/{order}/payment', [AdminOrderController::class, 'updatePayment'])->name('orders.payment.update');
        
        // Users
        Route::resource('users', AdminUserController::class);
        
        // Drivers
        Route::resource('drivers', AdminDriverController::class);
        Route::put('/drivers/{driver}/toggle-active', [AdminDriverController::class, 'toggleActive'])->name('drivers.toggle-active');
    });

// Driver routes
Route::middleware(['auth', \App\Http\Middleware\DriverMiddleware::class])
    ->prefix('driver')
    ->name('driver.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [DriverDashboardController::class, 'index'])->name('dashboard');
        
        // Delivery Management
        Route::get('/deliveries', [DriverDeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('/deliveries/assigned', [DriverDeliveryController::class, 'assigned'])->name('deliveries.assigned');
        Route::get('/deliveries/completed', [DriverDeliveryController::class, 'completed'])->name('deliveries.completed');
        Route::get('/deliveries/{delivery}', [DriverDeliveryController::class, 'show'])->name('deliveries.show');
        
        // Delivery Status Updates
        Route::put('/deliveries/{delivery}/pickup', [DriverDeliveryController::class, 'pickup'])->name('deliveries.pickup');
        Route::put('/deliveries/{delivery}/out-for-delivery', [DriverDeliveryController::class, 'outForDelivery'])->name('deliveries.out-for-delivery');
        Route::put('/deliveries/{delivery}/deliver', [DriverDeliveryController::class, 'deliver'])->name('deliveries.deliver');
        Route::put('/deliveries/{delivery}/fail', [DriverDeliveryController::class, 'fail'])->name('deliveries.fail');
        
        // Driver Profile Management
        Route::get('/profile/edit', [DriverProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [DriverProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/password', [DriverProfileController::class, 'showPasswordForm'])->name('profile.password');
        Route::put('/profile/password', [DriverProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('/profile/update-picture', [DriverProfileController::class, 'updateProfilePicture'])->name('profile.update-picture');
    });

// Driver Setup Route (for users marked as drivers but without driver profile)
Route::middleware(['auth'])
    ->prefix('driver')
    ->name('driver.')
    ->group(function () {
        Route::get('/setup', function() {
            // If user already has a driver profile, redirect to dashboard
            if (auth()->user()->driver) {
                return redirect()->route('driver.dashboard');
            }
            // If user is not a driver type, redirect to home
            if (auth()->user()->usertype !== 'driver') {
                return redirect()->route('home')->with('error', 'Your account is not authorized as a driver.');
            }
            return view('driver.setup');
        })->name('setup');
        
        Route::post('/profile', [DriverProfileController::class, 'store'])->name('profile.store');
    });
