<?php

use App\Http\Controllers\Freemius\CheckoutController;
use App\Http\Controllers\Freemius\PortalController;
use App\Http\Controllers\Freemius\FreemiusPaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/pricing', function () {
    return Inertia::render('Freemius/PricingPage');
})->middleware(['auth', 'verified'])->name('pricing');

Route::get('/account', function () {
    return Inertia::render('Freemius/AccountPage');
})->middleware(['auth', 'verified'])->name('portal.account');


Route::get('/payment/success', [FreemiusPaymentController::class, 'paymentSuccess'])->middleware(['auth', 'verified'])->name('payment.success');

Route::middleware(['auth'])->group(function () {
    Route::get('/api/checkout', [CheckoutController::class, 'checkout'])->name('freemius.checkout');
    Route::get('/api/portal', [PortalController::class, 'getPortal'])->name('freemius.portal');
    Route::get('/order/invoices/{id}', [CheckoutController::class, 'downloadInvoice'])->name('download.invoice');
});

