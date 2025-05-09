<?php

use App\Http\Controllers\Driver\DashboardController as DriverDashboardController;
use App\Http\Controllers\Driver\DeliveryController as DriverDeliveryController;
use App\Http\Controllers\Driver\ProfileController as DriverProfileController;
use Illuminate\Support\Facades\Route;

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
        Route::put('/deliveries/{delivery}/retry', [DriverDeliveryController::class, 'retry'])->name('deliveries.retry');
        
        // Payment Status Updates
        Route::post('/deliveries/{delivery}/payment', [DriverDeliveryController::class, 'updatePayment'])->name('deliveries.payment.update');
        
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
        // Add your driver setup routes here
    }); 