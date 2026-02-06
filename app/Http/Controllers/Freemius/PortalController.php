<?php

namespace App\Http\Controllers\Freemius;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PortalController extends Controller
{
    public function getPortal(Request $request)
    {
        $user = $request->user();
        $productId = config('freemius.product_id');
        $planId = config('freemius.plan_id');
        $fsUserId = 10069169;
        $priceId = 51769;

        // 1. Safety check
        if (! $fsUserId) {
            return response()->json(null); // Triggers <CustomerPortalEmpty /> in React
        }

        $baseUrl = "https://api.freemius.com/v1/products/{$productId}";
        $headers = ['Authorization' => 'Bearer '.config('freemius.bearer_token')];
        

        // 2. Fetch Data in Parallel (or sequence)
        $userResponse = Http::withHeaders($headers)->get("{$baseUrl}/users/{$fsUserId}.json");
        $subsResponse = Http::withHeaders($headers)->get("{$baseUrl}/users/{$fsUserId}/subscriptions.json");
        $plansResponse = Http::withHeaders($headers)->get("{$baseUrl}/plans.json");
        $paymentsResponse = Http::withHeaders($headers)->get("{$baseUrl}/users/{$fsUserId}/payments.json");

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
        ];
        foreach ($plans as &$plan) {

            $pricing = Http::withHeaders($headers)
                ->get("{$baseUrl}/plans/{$plan['id']}/pricing.json")
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

        // 3. Construct the "PortalData" object
        return response()->json([
            'user' => $userResponse->json(),
            'subscriptions' => [
                // React component looks for 'primary' to show the main card
                'primary' => $subsResponse->json('subscriptions.0'),
                'all' => $subsResponse->json('subscriptions'),
            ],
            'plans' => $plans,
            'payments' => $payments,
            'sellingUnit' => 'site', // or 'user'/'license'
        ]);
    }
}
