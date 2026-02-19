<?php

namespace App\Models\Freemius;

use App\Enums\RefundPolicy;
use Illuminate\Database\Eloquent\Model;

class FreemiusSetting extends Model
{
    protected $table = 'freemius_settings';

    protected $fillable = [
        'user_id', 'developer_id', 'developer_public_key', 'developer_secret_key', 'api_token', 'secret_key', 'public_key', 'freemius_product_id', 'title', 'slug', 'type',
        'icon', 'money_back_period', 'refund_policy', 'annual_renewals_discount', 'renewals_discount_type',
        'lifetime_license_proration_days', 'is_pricing_visible', 'accepted_payments', 'expose_license_key',
        'enable_after_purchase_email_login_link', 'freemius_payload', 'is_synced', 'public_url', 'base_url',
        'api_base_url',
    ];

    protected $casts = [
        'refund_policy' => RefundPolicy::class,
        'expose_license_key' => 'boolean',
        'enable_after_purchase_email_login_link' => 'boolean',
        'is_pricing_visible' => 'boolean',
        'is_synced' => 'boolean',
        'accepted_payments' => 'integer',
        'annual_renewals_discount' => 'integer',
        'money_back_period' => 'integer',
        'lifetime_license_proration_days' => 'integer',
    ];

    public function payment()
    {
        return $this->hasOne(FreemiusPayment::class, 'freemius_product_id', 'plugin_id');
    }
}
