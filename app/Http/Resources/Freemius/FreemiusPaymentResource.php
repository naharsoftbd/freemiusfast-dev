<?php

namespace App\Http\Resources\Freemius;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FreemiusPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->freemius_payment_id,

            // 🔹 User
            'user_id' => $this->fs_user_id,
            // 🔹 Freemius Identifiers
            'subscription_id'  => $this->subscription_id,
            'license_id'       => $this->license_id,
            'plan_id'          => $this->plan_id,
            'pricing_id'       => $this->pricing_id,
            'plugin_id'        => $this->plugin_id,
            'user_card_id'     => $this->user_card_id,
            'bound_payment_id' => $this->bound_payment_id,
            'external_id'      => $this->external_id,
            'product_title'    => $this->product?->title,

            // 🔹 Financial
            'gross'       => $this->gross,
            'gateway_fee' => $this->gateway_fee,
            'vat'         => $this->vat,
            'currency'    => $this->currency,
            'is_renewal'  => (bool) $this->is_renewal,
            'type'        => $this->type,

            // 🔹 Gateway
            'gateway'       => $this->gateway,
            'paymentMethod' => $this->payment_method,
            'environment'   => $this->environment,
            'source'        => $this->source,

            // 🔹 Location
            'ip'              => $this->ip,
            'country_code'    => $this->country_code,
            'zip_postal_code' => $this->zip_postal_code,
            'vat_id'          => $this->vat_id,
            'coupon_id'       => $this->coupon_id,

            // 🔹 Plan Info
            'planTitle' => $this->plan_title,
            'quota'     => $this->quota,

            // 🔹 URLs
            'invoiceUrl' => $this->invoice_url,

            // 🔹 Freemius Timestamps
            'createdAt' => optional($this->freemius_created_at)->toISOString(),
            'updatedAt' => optional($this->freemius_updated_at)->toISOString(),

            // 🔹 Local timestamps
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
