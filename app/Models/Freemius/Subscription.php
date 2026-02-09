<?php

namespace App\Models\Freemius;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id', 'fs_user_id', 'action', 'amount', 'billing_cycle', 'currency',
        'email', 'expiration', 'license_id', 'plan_id', 'pricing_id',
        'quota', 'subscription_id', 'payment_id', 'signature', 'tax',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expiration' => 'datetime',
        'billing_cycle' => 'integer',
        'quota' => 'integer',
        'tax' => 'integer',
    ];

    /**
     * Ensure currency is always stored in lowercase to
     * prevent crashes in your React frontend filter.
     */
    public function setCurrencyAttribute($value)
    {
        $this->attributes['currency'] = strtolower($value);
    }
}
