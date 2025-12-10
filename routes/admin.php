<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentMethodsController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;

use Illuminate\Support\Facades\Route;

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Menggunakan Route::resource untuk Payment Methods
    Route::resource('payment-methods', PaymentMethodsController::class)->except(['show']);
    // Rute resource lainnya tetap seperti sebelumnya
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('products', ProductController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('users', UserController::class);

    // Rute untuk Order
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
