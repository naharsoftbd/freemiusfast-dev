<?php

use App\Http\Controllers\Freemius\CheckoutController;
use App\Http\Controllers\Freemius\PortalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('freemius.checkout');
//     //Route::get('/portal', [PortalController::class, 'getPortal'])->name('freemius.portal');
// });
