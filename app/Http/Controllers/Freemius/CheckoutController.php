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
        $headers = ['Authorization' => 'Bearer ' . config('freemius.bearer_token')];

        $plansResponse = Http::withHeaders($headers)->get("{$baseUrl}/plans.json");

        if (!$response->successful()) {
            return response()->json(['error' => 'Failed to fetch plans'], 500);
        }

        $plans = $plansResponse->json('plans');
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
            
        }
        unset($plan);

        return response()->json([
    'options' => [
        'product_id' => (int) env('VITE_FREEMIUS_PRODUCT_ID'),
        'public_key' => env('VITE_FREEMIUS_PUBLIC_KEY'),
    ],
    'plans' => $plans
]);
    }
}
