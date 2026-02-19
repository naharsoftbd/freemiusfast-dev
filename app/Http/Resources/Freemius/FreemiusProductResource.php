<?php

namespace App\Http\Resources\Freemius;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FreemiusProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'freemius_product_id' => $this->freemius_product_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'type' => $this->type,
            'api_token' => $this->api_token,
            'icon' => $this->icon,
            'money_back_period' => $this->money_back_period,
            'refund_policy' => $this->refund_policy,
            'annual_renewals_discount' => $this->annual_renewals_discount,
            'renewals_discount_type' => $this->renewals_discount_type,
            'lifetime_license_proration_days' => $this->lifetime_license_proration_days,
            'is_pricing_visible' => $this->is_pricing_visible,
            'accepted_payments' => $this->accepted_payments,
            'expose_license_key' => $this->expose_license_key,
            'enable_after_purchase_email_login_link' => $this->enable_after_purchase_email_login_link,
            'is_synced' => $this->is_synced,
            'public_url' => $this->public_url,
            'base_url' => $this->base_url,
            'api_base_url' => $this->api_base_url,
            'developer_id' => $this->developer_id,
            'developer_public_key' => $this->developer_public_key,
            'developer_secret_key' => $this->developer_secret_key,
        ];
    }
}
