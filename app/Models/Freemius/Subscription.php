<?php

namespace App\Models\Freemius;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';
    
    protected $fillable = [
        'user_id',
        'plugin_id',
        'subscription_id',
        'license_id',
        'plan_id',
        'pricing_id',
        'plan_title',
        'renewal_amount',
        'initial_amount',
        'billing_cycle',
        'is_active',
        'renewal_date',
        'currency',
        'cancelled_at',
        'freemius_created_at',
        'checkout_upgrade_authorization',
        'quota',
        'payment_method',
        'upgrade_url',
        'is_trial',
        'trial_ends',
        'is_free_trial',
        'apply_renewal_cancellation_coupon_url',
        'cancel_renewal_url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_trial' => 'boolean',
        'is_free_trial' => 'boolean',
        'renewal_date' => 'datetime',
        'cancelled_at' => 'datetime',
        'trial_ends' => 'datetime',
        'freemius_created_at' => 'datetime',
        'payment_method' => 'array',
    ];

    public function plan()
    {
        return $this->belongsTo(FreemiusPlan::class);
    }
}
