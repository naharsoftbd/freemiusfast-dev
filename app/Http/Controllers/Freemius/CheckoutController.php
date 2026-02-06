<?php

namespace App\Http\Controllers\Freemius;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $productId = config('freemius.product_id');
        $publicKey = config('freemius.public_key');

        // 1. Fetch plans directly via HTTP
        // Note: Using the .json extension and with_pricing=true is required
        $baseUrl = "https://api.freemius.com/v1/products/{$productId}";
        $headers = ['Authorization' => 'Bearer '.config('freemius.bearer_token')];

        $plansResponse = Http::withHeaders($headers)->get("{$baseUrl}/plans.json");

        if (! $plansResponse->successful()) {
            return response()->json(['error' => 'Failed to fetch plans'], 500);
        }

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

        return response()->json([
            'options' => [
                'product_id' => (int) env('VITE_FREEMIUS_PRODUCT_ID'),
                'public_key' => env('VITE_FREEMIUS_PUBLIC_KEY'),
            ],
            'plans' => $plans,
        ]);
    }

    public function checkoutSuccess(Request $request)
    {
        dd($request->all());
    }

    public function downloadInvoice($paymentId)
    {
        $productId = config('freemius.product_id');
        $token = config('freemius.bearer_token');

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->get(
            "https://api.freemius.com/v1/products/{$productId}/payments/{$paymentId}/invoice.pdf"
        );

        if (! $response->successful()) {
            abort(404, 'Invoice not available');
        }

        return response($response->body(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice-'.$paymentId.'.pdf"',
        ]);
    }
}
