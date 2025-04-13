<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DriverController as AdminDriverController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\StripePaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // Admin Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Stripe webhook callback
        Route::get('/payment/callback', [StripePaymentController::class, 'handleCallback'])->name('payments.callback');
        
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

        // Review Management Routes
        Route::get('/reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('/reviews/{review}/reject', [App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('/reviews/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');
    }); 