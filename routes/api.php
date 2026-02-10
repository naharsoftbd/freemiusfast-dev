<?php

use App\Http\Controllers\Api\Freemius\FreemiusBillingController;
use App\Http\Controllers\Api\Freemius\FreemiusSubscriptionController;
use App\Http\Controllers\Api\Freemius\FreemiusUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('freemius-billings')->group(function () {
        Route::get('{fs_user_id}', [FreemiusBillingController::class, 'showByFsUserId']);
        Route::put('{fs_user_id}', [FreemiusBillingController::class, 'updateByFsUserId']);
    });

    Route::get('/user/me', [FreemiusUserController::class, 'me']);

    Route::delete('/subscriptions/{subscriptionId}/cancel', [FreemiusSubscriptionController::class, 'cancelSubscription'])->name('freemius.subscription.cancel');

});
