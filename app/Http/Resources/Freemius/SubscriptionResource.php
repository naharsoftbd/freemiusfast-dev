<?php

namespace App\Http\Resources\Freemius;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'subscriptionId' => (string) $this->subscription_id,
            'licenseId' => (string) $this->license_id,
            'planId' => (string) $this->plan_id,
            'pricingId' => (string) $this->pricing_id,

            'planTitle' => $this->plan_title ?? 'Unknown Plan',

            'renewalAmount' => (float) $this->renewal_amount,
            'initialAmount' => (float) $this->initial_amount,

            'billingCycle' => $this->billing_cycle,

            'isActive' => $this->cancelled_at === null,

            'renewalDate' => $this->renewal_date
                ? Carbon::parse($this->renewal_date)->toISOString()
                : null,

            'currency' => strtoupper($this->currency),

            'cancelledAt' => $this->cancelled_at
                ? Carbon::parse($this->cancelled_at)->toISOString()
                : null,

            'createdAt' => $this->freemius_created_at
                ? Carbon::parse($this->freemius_created_at)->toISOString()
                : null,

            'checkoutUpgradeAuthorization' => $this->checkout_upgrade_authorization,

            'quota' => $this->quota,

            'paymentMethod' => $this->payment_method,

            'upgradeUrl' => url("/subscriptions/{$this->subscription_id}/upgrade"),

            'isTrial' => $this->is_trial,

            'trialEnds' => $this->trial_ends
                ? Carbon::parse($this->trial_ends)->toISOString()
                : null,

            'isFreeTrial' => $this->is_free_trial,

            'applyRenewalCancellationCouponUrl' =>
                $this->apply_renewal_cancellation_coupon_url,

            'cancelRenewalUrl' =>
                url("/subscriptions/{$this->subscription_id}/cancel"),

            'sandboxParam' => app(\App\Services\Freemius\FreemiusService::class)
                ->getSandBoxParam() ?? [],
        ];
    }
}
