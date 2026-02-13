<?php

namespace App\Services\Freemius;

use App\Models\User;
use App\Traits\FreemiusConfigTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class FreemiusService
{
    use FreemiusConfigTrait;

    protected $freemiusBillingService;

    /**
     * Create a new class instance.
     */
    public function __construct(FreemiusBillingService $freemiusBillingService)
    {
        $this->initFreemius(); // initialize shared Freemius config
        $this->freemiusBillingService = $freemiusBillingService;

    }

    /**
     * Verify that the request came from Freemius.
     */
    public function isSignatureValid($clean_url, $receivedSignature): bool
    {
        // Freemius typically expects the signature to be an HMAC-SHA256
        // hash of the JSON-encoded data using your secret key.
        $expectedSignature = hash_hmac('sha256', $clean_url, $this->secretKey);

        return hash_equals($expectedSignature, $receivedSignature);
    }

    // Download Freemius Invoice
    public function downloadInvoice($paymentId)
    {

        $response = Http::withHeaders($this->headers)->get(
            "{$this->baseUrl}/payments/{$paymentId}/invoice.pdf"
        );

        return $response;
    }

    // Ccheckout API Indpoint data
    public function checkout()
    {
        return $this->getPlanData();
    }

    // Get All Plan Data
    public function getPlanData()
    {
        $plansResponse = Http::withHeaders($this->headers)->get("{$this->baseUrl}/plans.json");

        $plans = $plansResponse->json('plans');

        foreach ($plans as &$plan) {

            $pricing = Http::withHeaders($this->headers)
                ->get("{$this->baseUrl}/plans/{$plan['id']}/pricing.json")
                ->json('pricing');

            $plan['pricing'] = collect($pricing)->map(function ($price) {
                return [
                    ...$price,
                    'currency' => $price['currency'],
                ];
            })->values()->all();

            // ✅ Get Features dynamically
            $featuresResponse = Http::withHeaders($this->headers)
                ->get("{$this->baseUrl}/plans/{$plan['id']}/features.json");

            $features = $featuresResponse->json('features');

            $plan['features'] = collect($features)->map(function ($feature) {
                return [
                    'id' => $feature['id'],
                    'title' => $feature['title'],
                    'description' => $feature['description'],
                    'is_featured' => $feature['is_featured'],
                    'value' => $feature['value'],
                ];
            })->values()->all();


            $plan['sandboxParam'] = $this->getSandBoxParam() ?? [];

        }

        unset($plan);

        return $plans;

    }

    // Customer Portal API Indpoint Data
    public function getPortalData()
    {
        $user = Auth::user();
        $plans = $this->getPlanData();
        $payments = $this->getPaymentData($plans);
        // 1. Safety check
        if (! $this->getFsUserId()) {
            return response()->json([
                'user' => $user,
                'subscriptions' => [
                    // React component looks for 'primary' to show the main card
                    'primary' => [],
                    'all' => [],
                ],
                'plans' => $plans,
                'payments' => $payments,
                'sellingUnit' => 'site', // or 'user'/'license'
            ]);
        }

        // 2. Fetch Data in Parallel (or sequence)
        $userResponse = Http::withHeaders($this->headers)->get("{$this->baseUrl}/users/{$this->getFsUserId()}.json");
        $subsResponse = Http::withHeaders($this->headers)->get("{$this->baseUrl}/users/{$this->getFsUserId()}/subscriptions.json");

        $rawSubscriptions = collect($subsResponse->json('subscriptions'));
        $primaryRaw = $rawSubscriptions->first();

        $primary = $primaryRaw
            ? $this->mapPortalSubscription($primaryRaw, $plans)
            : null;

        $past = $rawSubscriptions
            ->map(fn ($sub) => $this->mapPortalSubscription($sub, $plans))
            ->values();

        $billing = $this->getUserBilling();

        // 3. Construct the "PortalData" object
        return response()->json([
            'user' => $userResponse->json(),
            'subscriptions' => [
                // React component looks for 'primary' to show the main card
                'primary' => $primary,
                'active' => $primary,
                'past' => $past,
            ],
            'plans' => $plans,
            'payments' => $payments,
            'billing' => $billing,
            'sellingUnit' => 'site', // or 'user'/'license'
        ]);
    }

    // Get payment data by plan
    public function getPaymentData($plans)
    {
        $paymentsResponse = Http::withHeaders($this->headers)->get("{$this->baseUrl}/users/{$this->getFsUserId()}/payments.json");

        $planMap = collect($plans)->keyBy('id');
        $payments = collect($paymentsResponse->json('payments'))->map(function ($payment) use ($planMap) {
            $plan = $planMap->get((int) $payment['plan_id']);
            $publicUrl = config('freemius.public_url');

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

                'quota' => $plan['licenses'] ?? null,
                'planTitle' => $plan['title'] ?? 'Unknown Plan',
            ];
        })->values();

        return $payments;

    }

    // Customer Portal Subscription data by Plan
    protected function mapPortalSubscription(array $sub, array $plans): array
    {
        $plan = collect($plans)->firstWhere('id', (int) $sub['plan_id']);

        return [
            'subscriptionId' => (string) $sub['id'],
            'licenseId' => (string) $sub['license_id'],
            'planId' => (string) $sub['plan_id'],
            'pricingId' => (string) $sub['pricing_id'],

            'planTitle' => $plan['title'] ?? 'Unknown Plan',

            'renewalAmount' => (float) $sub['renewal_amount'],
            'initialAmount' => (float) $sub['initial_amount'],

            'billingCycle' => $this->mapBillingCycle($sub['billing_cycle'] ?? null),

            'isActive' => $sub['canceled_at'] === null,

            'renewalDate' => $sub['next_payment']
                ? \Carbon\Carbon::parse($sub['next_payment'])->toISOString()
                : null,

            'currency' => strtoupper($sub['currency']),

            'cancelledAt' => $sub['canceled_at']
                ? \Carbon\Carbon::parse($sub['canceled_at'])->toISOString()
                : null,

            'createdAt' => \Carbon\Carbon::parse($sub['created'])->toISOString(),

            'checkoutUpgradeAuthorization' => $this->getUpgradeAuth($sub['license_id'], $sub['plan_id']),

            'quota' => $plan['licenses'] ?? null,

            'paymentMethod' => $sub['gateway']
                ? [
                    'type' => match ($sub['gateway']) {
                        'stripe' => 'card',
                        'paypal' => 'paypal',
                        default => 'unknown',
                    },
                ]
                : null,

            'upgradeUrl' => url("/subscriptions/{$sub['id']}/upgrade"),

            'isTrial' => $sub['trial_ends'] !== null,

            'trialEnds' => $sub['trial_ends']
                ? \Carbon\Carbon::parse($sub['trial_ends'])->toISOString()
                : null,

            'isFreeTrial' => $sub['trial_ends'] !== null,

            'applyRenewalCancellationCouponUrl' => null,

            'cancelRenewalUrl' => url("/subscriptions/{$sub['id']}/cancel"),

            'sandboxParam' => $this->getSandBoxParam() ?? [],
        ];
    }

    // Customer Portal Subscription data billingCycle Format
    protected function mapBillingCycle(?int $cycle): string
    {
        return match ($cycle) {
            1 => 'monthly',
            12 => 'yearly',
            0 => 'oneoff',
            default => 'N/A',
        };
    }

    // Customer Portal billing information
    protected function getUserBilling(): array
    {
        $response = Http::withHeaders($this->headers)->get("{$this->baseUrl}/users/{$this->getFsUserId()}/billing.json");

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
        ];

        // $billing = $this->freemiusBillingService->updateByFsUserId($billing, $this->fsUserId);

        return $billing;
    }

    public function getUpgradeAuth($licenseId, $planId)
    {
        // Freemius API usually requires HMAC or Bearer Token
        $response = Http::withHeaders($this->headers)
            ->post("{$this->baseUrl}/licenses/{$licenseId}/checkout/link.json", [
                'plan_id' => $planId,
                // 'billing_cycle' => 'annual', (optional)
            ]);

        if ($response->successful()) {
            $data = $response->json();

            // The token is often part of the generated URL or returned in the 'authorization' field
            return $data['settings']['authorization'] ?? null;
        }

        return null;
    }

    // Get Sandbox Param
    public function getSandBoxParam()
    {
        $product_id = $this->productId;
        $product_public_key = $this->publicKey;
        $product_secret_key = $this->secretKey;

        $ctx = time(); // Or any random unique string
        $sandbox_token = md5(
            $ctx.
            $product_id.
            $product_secret_key.
            $product_public_key.
            'checkout'
        );

        return [
            'token' => $sandbox_token,
            'ctx' => $ctx,
        ];
    }
}
