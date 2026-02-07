<?php

namespace App\Services\Freemius;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FreemiusService
{
    protected  $accessToken;

    protected  $secretKey;

    protected  $productId;

    protected  $planId;

    protected  $headers;

    protected  $apiBaseUrl;

    protected  $publicKey;

    protected  $baseUrl;

    protected  $fsUserId;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->accessToken = config('freemius.bearer_token');
        $this->secretKey = config('freemius.secret_key');
        $this->productId = config('freemius.product_id');
        $this->headers = ['Authorization' => 'Bearer '.config('freemius.bearer_token')];
        $this->apiBaseUrl = config('freemius.api_base_url');
        $this->publicKey = config('freemius.public_key');
        $this->baseUrl = "{$this->apiBaseUrl}/{$this->productId}";
        $this->fsUserId = User::find(auth()->id())->subscription->fs_user_id;
        
    }

    /**
     * Verify that the request came from Freemius.
     */
    public function isSignatureValid(array $data, $receivedSignature): bool
    {
        
        // Remove signature from data to hash the content only
        unset($data['signature']);

        ksort($data);

        // Freemius typically expects the signature to be an HMAC-SHA256 
        // hash of the JSON-encoded data using your secret key.
        $expectedSignature = hash_hmac('sha256', json_encode($data), $this->secretKey);

        return hash_equals($expectedSignature, $receivedSignature);
    }

    public function downloadInvoice($paymentId)
    {

       $response = Http::withHeaders($this->headers)->get(
            "{$this->baseUrl}/payments/{$paymentId}/invoice.pdf"
        );

        return $response;
    }

    public function checkout()
    {      
        return $this->getPlanData();
    }

    public function getPlanData()
    {
        $plansResponse = Http::withHeaders($this->headers)->get("{$this->baseUrl}/plans.json");

        $plans = $plansResponse->json('plans');

        $planFeatures = [
            39394 => [ // Plan ID
                ['title' => 'Unlimited Patients', 'included' => true],
                ['title' => 'Digital Prescriptions', 'included' => true],
                ['title' => 'Multi-Doctor Support', 'included' => true],
                ['title' => 'Email Support', 'included' => true],
            ],
            39400 => [
                ['title' => 'Unlimited Patients', 'included' => true],
                ['title' => 'Digital Prescriptions', 'included' => true],
                ['title' => 'Multi-Doctor Support', 'included' => false],
                ['title' => 'Priority Support', 'included' => false],
            ],
            39873 => [
                ['title' => 'Unlimited Patients', 'included' => true],
                ['title' => 'Digital Prescriptions', 'included' => true],
                ['title' => 'Multi-Doctor Support', 'included' => false],
                ['title' => 'Priority Support', 'included' => false],
            ],
        ];

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

            $plan['features'] = $planFeatures[$plan['id']] ?? [];

        }
        unset($plan);

        return $plans;

    }

     public function getPortalData()
    {
        $user = Auth::user();
        // 1. Safety check
        if (!$this->fsUserId) {
            return response()->json([
            'user' => $user,
            'subscriptions' => [
                // React component looks for 'primary' to show the main card
                'primary' => [],
                'all' => [],
            ],
            'plans' => [],
            'payments' => [],
            'sellingUnit' => 'site', // or 'user'/'license'
        ]);
        }
        

        // 2. Fetch Data in Parallel (or sequence)
        $userResponse = Http::withHeaders($this->headers)->get("{$this->baseUrl}/users/{$this->fsUserId}.json");
        $subsResponse = Http::withHeaders($this->headers)->get("{$this->baseUrl}/users/{$this->fsUserId}/subscriptions.json");

        $plans = $this->getPlanData();

        $payments = $this->getPaymentData($plans);

        $rawSubscriptions = collect($subsResponse->json('subscriptions'));
        $primaryRaw = $rawSubscriptions->first();

        $primary = $primaryRaw
            ? $this->mapPortalSubscription($primaryRaw, $plans)
            : null;

        $past = $rawSubscriptions
            ->map(fn ($sub) => $this->mapPortalSubscription($sub, $plans))
            ->values();

        $billing = $this->getUserBilling();

        $billing = response()->json([
            'billing' => [
                'business_name'   => $billing['business_name'] ?? null,
                'phone'           => $billing['phone'] ?? null,
                'tax_id'          => $billing['tax_id'] ?? null,
                'address_street'  => $billing['address_street'] ?? null,
                'address_apt'     => $billing['address_apt'] ?? null,
                'address_city'    => $billing['address_city'] ?? null,
                'address_state'   => $billing['address_state'] ?? null,
                'address_zip'     => $billing['address_zip'] ?? null,
                'address_country' => $billing['address_country'] ?? null,
            ],
            'user' => [
                'id' => $this->fsUserId, // Freemius user id
            ],
        ]);
        

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

    public function getPaymentData($plans)
    {
        $paymentsResponse = Http::withHeaders($this->headers)->get("{$this->baseUrl}/users/{$this->fsUserId}/payments.json");

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

                'quota' => $plan['quota'] ?? null,
                'planTitle' => $plan['title'] ?? 'Unknown Plan',
            ];
        })->values();

        return $payments;

    }

    protected function mapPortalSubscription(array $sub, array $plans): array
    {
        $plan = collect($plans)->firstWhere('id', (int) $sub['plan_id']);

        return [
            'subscriptionId' => (string) $sub['id'],
            'licenseId'      => (string) $sub['license_id'],
            'planId'         => (string) $sub['plan_id'],
            'pricingId'      => (string) $sub['pricing_id'],

            'planTitle'      => $plan['title'] ?? 'Unknown Plan',

            'renewalAmount'  => (float) $sub['renewal_amount'],
            'initialAmount'  => (float) $sub['initial_amount'],

            'billingCycle'   => $this->mapBillingCycle($sub['billing_cycle'] ?? null),

            'isActive'       => $sub['canceled_at'] === null,

            'renewalDate'    => $sub['next_payment']
                ? \Carbon\Carbon::parse($sub['next_payment'])->toISOString()
                : null,

            'currency'       => strtoupper($sub['currency']),

            'cancelledAt'    => $sub['canceled_at']
                ? \Carbon\Carbon::parse($sub['canceled_at'])->toISOString()
                : null,

            'createdAt'      => \Carbon\Carbon::parse($sub['created'])->toISOString(),

            'checkoutUpgradeAuthorization' => null,

            'quota' => $plan['quota'] ?? null,

            'paymentMethod' => $sub['gateway']
                ? [
                    'type' => match ($sub['gateway']) {
                        'stripe' => 'card',
                        'paypal' => 'paypal',
                        default  => 'unknown',
                    }
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
        ];
    }

    protected function mapBillingCycle(?int $cycle): string
    {
        return match ($cycle) {
            1   => 'monthly',
            12  => 'yearly',
            0   => 'oneoff',
            default => 'N/A',
        };
    }

    protected function getUserBilling(): array
    {
        $response = Http::withHeaders($this->headers)->get("{$this->baseUrl}/users/{$this->fsUserId}/billing.json");

        if (!$response->successful()) {
            throw new \Exception('Unable to fetch billing info from Freemius');
        }

        return $response->json();
    }


}
