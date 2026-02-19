<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('product-updates', function ($user) {
    return true; // Or add logic: return $user->id === $product->user_id;
});

Broadcast::channel('customerdata-update', function ($user) {
    return true; // Or add logic: return $user->id === $product->user_id;
});

