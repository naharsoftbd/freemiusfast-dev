<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Freemius\PortalController;
use App\Http\Controllers\Freemius\CheckoutController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/pricing', function () {
    return Inertia::render('PricingPage');
})->middleware(['auth', 'verified'])->name('pricing');

Route::get('/account', function () {
    return Inertia::render('AccountPage');
})->middleware(['auth', 'verified'])->name('portal.account');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/success', [CheckoutController::class, 'checkoutSuccess'])->middleware(['auth', 'verified'])->name('order.success');
Route::inertia('/payment/success', 'PaymentSuccess');
Route::inertia('/payment/cancel', 'PaymentCancel');

Route::middleware(['auth'])->group(function () {
    // Calling this as /api/portal
    Route::get('/api/checkout', [CheckoutController::class, 'checkout'])->name('freemius.checkout');
    Route::get('/api/portal', [PortalController::class, 'getPortal']);
});


require __DIR__.'/auth.php';
