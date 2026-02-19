<?php

namespace App\Services\Freemius;

use App\Models\User;
use App\Traits\FreemiusConfigTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\PlanResource;
use App\Services\Freemius\FreemiusService;
use App\Models\Freemius\Subscription;
use Illuminate\Support\Carbon;
use App\Models\Freemius\UserFsEntitlement;
use App\Services\Freemius\FreemiusBillingService;
use App\Models\Freemius\FreemiusPayment;
use Illuminate\Support\Facades\Log;

class FreemiusCustomerService
{
    use FreemiusConfigTrait;

    protected $freemiusBillingService;

    protected $freemiusService;

    /**
     * Create a new class instance.
     */
    public function __construct(FreemiusBillingService $freemiusBillingService, FreemiusService $freemiusService)
    {
        $this->initFreemius(); // initialize shared Freemius config
        $this->freemiusBillingService = $freemiusBillingService;
        $this->freemiusService = $freemiusService;
    }

    public function syncCustomerData($user_id)
    {
        $fsUserId = UserFsEntitlement::where('user_id', $user_id)->first()->fs_user_id;

        $plans = PlanResource::collection($this->freemiusService->getPlanData());

        Log::withContext(['user_id' => $fsUserId])->info('User activity recorded.');
        $subsResponse = $this->client()->get("{$this->baseUrl}/users/{$fsUserId}/subscriptions.json");

        $rawSubscriptions = collect($subsResponse->json('subscriptions'));

        $subscriptions = $rawSubscriptions
            ->map(fn ($sub) => $this->freemiusService->mapPortalSubscription($sub, $plans))
            ->values();
        Log::info('User activity recorded.', [
    'subscriptions' => $subscriptions->toArray(),
]);
        foreach ($subscriptions as $sub) {
                Subscription::updateOrCreate(
                    [
                        // UNIQUE KEY (match condition)
                        'subscription_id' => $sub['subscriptionId'],
                    ],
                    [
                        'user_id' => $user_id,
                        'plugin_id' => $sub['plugin_id'],
                        'license_id' => $sub['licenseId'],
                        'plan_id' => $sub['planId'],
                        'pricing_id' => $sub['pricingId'],
                        'plan_title' => $sub['planTitle'],

                        'renewal_amount' => $sub['renewalAmount'],
                        'initial_amount' => $sub['initialAmount'],
                        'billing_cycle' => $sub['billingCycle'],

                        'is_active' => $sub['isActive'],

                        'renewal_date' => $sub['renewalDate'] ?? null,

                        'currency' => $sub['currency'],

                        'cancelled_at' => $sub['cancelledAt'] ?? null,

                        'freemius_created_at' => $sub['createdAt'] ?? null,

                        'checkout_upgrade_authorization' => $sub['checkoutUpgradeAuthorization'] ?? null,

                        'quota' => $sub['quota'],

                        'payment_method' => $sub['paymentMethod'] ?? null,

                        'upgrade_url' => $sub['upgradeUrl'] ?? null,

                        'is_trial' => $sub['isTrial'] ?? false,

                        'trial_ends' => $sub['trialEnds'] ?? null,

                        'is_free_trial' => $sub['isFreeTrial'] ?? false,

                        'apply_renewal_cancellation_coupon_url' =>
                            $sub['applyRenewalCancellationCouponUrl'] ?? null,

                        'cancel_renewal_url' => $sub['cancelRenewalUrl'] ?? null,
                    ]
                );
            }

            // Billing address update
            $this->getUserBilling($fsUserId, $user_id);
            $this->getPaymentData($plans, $user_id, $fsUserId);

    }

    protected function getUserBilling($fsUserId, $user_id)
    {
        $response = $this->client()->get("{$this->baseUrl}/users/{$fsUserId}/billing.json");

        if (! $response->successful()) {
            throw new \Exception('Unable to fetch billing info from Freemius');
        }

        $rawBilling = $response->json();

        $billing = [
            'business_name' => $rawBilling['business_name'] ?? null,
            'email' => $rawBilling['email'] ?? null,
            'first' => $rawBilling['first'] ?? 'Abu',
            'last' => $rawBilling['last'] ?? 'Salah',
            'phone' => $rawBilling['phone'] ?? null,
            'tax_id' => $rawBilling['tax_id'] ?? null,
            'address_street' => $rawBilling['address_street'] ?? null,
            'address_apt' => $rawBilling['address_apt'] ?? null,
            'address_city' => $rawBilling['address_city'] ?? null,
            'address_state' => $rawBilling['address_state'] ?? null,
            'address_zip' => $rawBilling['address_zip'] ?? null,
            'address_country' => $rawBilling['address_country'] ?? null,
            'address_country_code' => $rawBilling['address_country_code'] ?? null,
            'fs_user_id' => $fsUserId,
        ];

        $billing = $this->freemiusBillingService->updateByUserId($billing, $user_id);

        return $billing;
    }

    // Get payment data by plan
    public function getPaymentData($plans, $user_id, $fsUserId)
    {
        $paymentsResponse = $this->client()->get("{$this->baseUrl}/users/{$fsUserId}/payments.json");

        $planMap = collect($plans)->keyBy('plan_id');
        $payments = collect($paymentsResponse->json('payments'))->map(function ($payment) use ($planMap) {
            $plan = $planMap->get((int) $payment['plan_id']);
            $publicUrl = $this->publicUrl;
            $pricing = $plan->pricings->where('pricing_id', (int) $payment['pricing_id'])->first();
            return [
                // keep original fields if needed
                ...$payment,

                // ✅ REQUIRED BY PortalPayment TYPE
                'createdAt' => \Carbon\Carbon::parse($payment['created'])->toISOString(),

                'paymentMethod' => match ($payment['gateway']) {
                    'stripe' => 'card',
                    'paypal' => 'paypal',
                    default => 'unknown',
                },

                'invoiceUrl' => "{$publicUrl}/order/invoices/{$payment['id']}",

                'quota' => $pricing->licenses ?? null,
                'planTitle' => $plan['title'] ?? 'Unknown Plan',
            ];
        })->values();

        foreach ($payments as $payment) {

                FreemiusPayment::updateOrCreate(
                    [
                        'freemius_payment_id' => $payment['id'],
                    ],
                    [
                        'user_id' => $user_id,
                        'fs_user_id' => $payment['user_id'],
                        'subscription_id' => $payment['subscription_id'],
                        'license_id' => $payment['license_id'],
                        'plan_id' => $payment['plan_id'],
                        'pricing_id' => $payment['pricing_id'],
                        'plugin_id' => $payment['plugin_id'],
                        'user_card_id' => $payment['user_card_id'],
                        'bound_payment_id' => $payment['bound_payment_id'],
                        'external_id' => $payment['external_id'],
                        'gross' => $payment['gross'],
                        'gateway_fee' => $payment['gateway_fee'],
                        'vat' => $payment['vat'],
                        'currency' => $payment['currency'],
                        'is_renewal' => $payment['is_renewal'],
                        'type' => $payment['type'],
                        'gateway' => $payment['gateway'],
                        'payment_method' => $payment['paymentMethod'],
                        'environment' => $payment['environment'],
                        'source' => $payment['source'],
                        'ip' => $payment['ip'],
                        'country_code' => $payment['country_code'],
                        'zip_postal_code' => $payment['zip_postal_code'],
                        'vat_id' => $payment['vat_id'],
                        'coupon_id' => $payment['coupon_id'],
                        'plan_title' => $payment['planTitle'],
                        'quota' => $payment['quota'],
                        'invoice_url' => $payment['invoiceUrl'],
                        'freemius_created_at' => \Carbon\Carbon::parse($payment['createdAt']),
                        'freemius_updated_at' => isset($payment['updated'])
                            ? \Carbon\Carbon::parse($payment['updated'])
                            : null,
                    ]
            );

        }
    }
}
