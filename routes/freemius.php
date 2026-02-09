<?php

use App\Http\Controllers\Freemius\CheckoutController;
use App\Http\Controllers\Freemius\PortalController;
use App\Http\Controllers\Freemius\FreemiusPaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/pricing', function () {
    return Inertia::render('Freemius/PricingPage');
})->name('freemius.pricing');

Route::get('/account', function () {
    return Inertia::render('Freemius/AccountPage');
})->middleware(['auth', 'verified'])->name('portal.account');


Route::middleware(['auth'])->group(function () {
    Route::get('/api/checkout', [CheckoutController::class, 'checkout'])->name('freemius.checkout');
    Route::get('/api/portal', [PortalController::class, 'getPortal'])->name('freemius.portal');
    Route::get('/order/invoices/{id}', [PortalController::class, 'downloadInvoice'])->name('download.invoice');

    Route::get('/payment/success', [FreemiusPaymentController::class, 'paymentSuccess'])->name('payment.success');
});

