<?php

use App\Http\Controllers\Freemius\CheckoutController;
use App\Http\Controllers\Freemius\FreemiusPaymentController;
use App\Http\Controllers\Freemius\PortalController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/pricing', function () {
    return Inertia::render('Freemius/PricingPage');
})->name('freemius.pricing');

Route::get('/account', function () {
    return Inertia::render('Freemius/AccountPage');
})->middleware(['auth', 'verified'])->name('portal.account');


Route::get('/checkout', function () {
    return Inertia::render('Freemius/Checkout');
})->name('freemius.checkout');
Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('freemius.checkout.store');

// Without Auth for Hosted Checkout
Route::get('/payment/success', [FreemiusPaymentController::class, 'paymentSuccess'])->name('payment.success');

Route::middleware(['auth'])->group(function () {
    Route::get('/api/checkout', [CheckoutController::class, 'apiCheckout'])->name('freemius.api.checkout');
    Route::get('/api/portal', [PortalController::class, 'getPortal'])->name('freemius.portal');
    Route::get('/order/invoices/{id}', [PortalController::class, 'downloadInvoice'])->name('download.invoice');
});
