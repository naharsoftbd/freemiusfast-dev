<?php

namespace App\Models\Freemius;

use Illuminate\Database\Eloquent\Model;
use App\Enums\RefundPolicy;

class FreemiusProduct extends Model
{
    protected $table = 'freemius_products';

    protected $fillable = [
        'user_id', 'api_token', 'secret_key', 'public_key', 'freemius_product_id', 'title', 'slug', 'type', 
        'icon', 'money_back_period', 'refund_policy', 'annual_renewals_discount', 'renewals_discount_type',
        'lifetime_license_proration_days', 'is_pricing_visible', 'accepted_payments', 'expose_license_key', 
        'enable_after_purchase_email_login_link', 'freemius_payload', 'is_synced'
    ];

    protected $casts = [
        'refund_policy' => RefundPolicy::class,
        'expose_license_key' => 'boolean',
        'enable_after_purchase_email_login_link' => 'boolean',
        'is_pricing_visible'   => 'boolean',
        'is_synced'      => 'boolean',
        'accepted_payments' => 'integer',
        'annual_renewals_discount' => 'integer',
        'money_back_period' => 'integer',
        'lifetime_license_proration_days' => 'integer',
    ];
}
