<?php

namespace App\Models\Freemius;

use Illuminate\Database\Eloquent\Model;

class FreemiusPayment extends Model
{
    protected $fillable = [
        'user_id',
        'fs_user_id',
        'freemius_payment_id',
        'subscription_id',
        'license_id',
        'plan_id',
        'pricing_id',
        'plugin_id',
        'user_card_id',
        'bound_payment_id',
        'external_id',
        'gross',
        'gateway_fee',
        'vat',
        'currency',
        'is_renewal',
        'type',
        'gateway',
        'payment_method',
        'environment',
        'source',
        'ip',
        'country_code',
        'zip_postal_code',
        'vat_id',
        'coupon_id',
        'plan_title',
        'quota',
        'invoice_url',
        'freemius_created_at',
        'freemius_updated_at',
    ];

    protected $casts = [
        'gross' => 'decimal:2',
        'gateway_fee' => 'decimal:2',
        'vat' => 'decimal:2',
        'is_renewal' => 'boolean',
        'environment' => 'integer',
        'source' => 'integer',
        'quota' => 'integer',
        'freemius_created_at' => 'datetime',
        'freemius_updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(FreemiusSetting::class, 'plugin_id', 'freemius_product_id');
    }
}
