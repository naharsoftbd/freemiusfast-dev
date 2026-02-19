<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->plan_id, // Use Freemius ID as public ID

            'name' => $this->name,
            'title' => $this->title,
            'description' => $this->description,

            'is_featured' => (bool) $this->is_featured,
            'is_hidden' => (bool) $this->is_hidden,
            'is_free' => (bool) $this->is_free,
            'is_block_features' => (bool) $this->is_block_features,

            'trial_days' => $this->trial_period,

            'created' => optional($this->freemius_created_at)?->format('Y-m-d H:i:s'),
            'updated' => optional($this->freemius_updated_at)?->format('Y-m-d H:i:s'),

            'pricing' => PricingResource::collection(
                $this->whenLoaded('pricings')
            ),

            'features' => FeatureResource::collection(
                $this->whenLoaded('features')
            ),
            'sandboxParam' => $this->sandboxParam,
        ];
    }
}
