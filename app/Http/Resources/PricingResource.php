<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PricingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->pricing_id, // Use Freemius ID as public ID
            'plan_id' => $this->plan_id,
            'licenses' => $this->licenses,
            'monthly_price' => $this->monthly_price,
            'annual_price' => $this->annual_price,
            'lifetime_price' => $this->lifetime_price,
            'currency' => $this->currency,
            'is_whitelabeled' => $this->is_whitelabeled,
            'is_hidden' => $this->is_hidden,
            'created' => optional($this->freemius_created_at)?->format('Y-m-d H:i:s'),
            'updated' => optional($this->freemius_updated_at)?->format('Y-m-d H:i:s'),
        ];
    }
}
