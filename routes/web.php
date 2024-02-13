<?php

use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MoveToCartController;
use App\Http\Controllers\SaveForLaterController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardSettingsProfileController;
use App\Http\Controllers\DashboardSettingsPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardOrderController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// SHOP
Route::get('/shop', ShopController::class)->name('shop');
Route::get('/', ShopController::class)->name('shop');

// PRODUCTS
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('product.show');

Route::middleware(['auth'])->group(function () {
    // CART
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('cart.index');
        Route::post('/{product:id}', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/{product:id}/increase', [CartController::class, 'increase'])->name('cart.increase');
        Route::patch('/{product:id}/decrease', [CartController::class, 'decrease'])->name('cart.decrease');
        Route::delete('/empty', [CartController::class, 'empty'])->name('cart.empty');
        Route::delete('/{product:id}', [CartController::class, 'destroy'])->name('cart.destroy');

        // SAVEFORLATER
        Route::post('/{id}/save-for-later', SaveForLaterController::class)->name('cart.saveForLater');
        // MOVETOCART
        Route::post('/{id}/move-to-cart', MoveToCartController::class)->name('cart.moveToCart');
    });

    // WISHLIST
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product:id}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/wishlist/{product:id}/move-to-cart', [WishlistController::class, 'moveToCart'])->name('wishlist.moveToCart');
    Route::delete('/wishlist', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    // CHECKOUT
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/failure', [CheckoutController::class, 'failure'])->name('checkout.failure');

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/orders', [DashboardOrderController::class, 'index'])->name('order.index');
    Route::put('/dashboard/{order}/orders', [DashboardOrderController::class, 'update'])->name('order.update');

    // PRODUCTS
    Route::get('/dashboard/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/dashboard/products/create', [ProductController::class, 'create'])->name('products.create');

    // DASHBOARD SETTINGS
    Route::get('/dashboard/settings/profile', [DashboardSettingsProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::patch('/dashboard/settings/profile', [DashboardSettingsProfileController::class, 'update'])->name('settings.profile.update');

    Route::get('/dashboard/settings/password', [DashboardSettingsPasswordController::class, 'edit'])->name('settings.password.edit');
    Route::patch('/dashboard/settings/password', [DashboardSettingsPasswordController::class, 'update'])->name('settings.password.update');
});

// STRIPE WEBHOOK
Route::post('/webhook', [CheckoutController::class, 'webhook'])->name('checkout.webhook');

require __DIR__ . '/auth.php';
