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
        if (!$fsUserId) {
            return response()->json(null); // Triggers <CustomerPortalEmpty /> in React
        }

        $baseUrl = "https://api.freemius.com/v1/products/{$productId}";
        $headers = ['Authorization' => 'Bearer ' . config('freemius.bearer_token')];

        // 2. Fetch Data in Parallel (or sequence)
        $subsResponse = Http::withHeaders($headers)->get("{$baseUrl}/users/{$fsUserId}/subscriptions.json");
        $plansResponse = Http::withHeaders($headers)->get("{$baseUrl}/plans.json");
        $plansPriceResponse = Http::withHeaders($headers)->get("{$baseUrl}/plans/{$planId}/pricing.json");
        $paymentsResponse = Http::withHeaders($headers)->get("{$baseUrl}/users/{$fsUserId}/payments.json");
        
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



        // 3. Construct the "PortalData" object
        return response()->json([
            'user' => [
                'email' => $user->email,
                'id' => $fsUserId,
            ],
            'subscriptions' => [
                // React component looks for 'primary' to show the main card
                'primary' => $subsResponse->json('subscriptions.0'), 
                'all' => $subsResponse->json('subscriptions'),
            ],
            'plans' => $plans,
            'payments' => $paymentsResponse->json('payments'),
            'sellingUnit' => 'site', // or 'user'/'license'
        ]);
    }
}
