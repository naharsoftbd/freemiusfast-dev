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
        $response = Http::withHeaders([
            // Use the "API Bearer Authorization Token" from Freemius Dashboard Settings
            'Authorization' => 'Bearer ' . config('freemius.bearer_token'),
        ])->get("https://api.freemius.com/v1/products/{$productId}/plans/" .config('freemius.plan_id').".json", [
            'with_pricing' => 'true'
        ]);

        if (!$response->successful()) {
            return response()->json(['error' => 'Failed to fetch plans'], 500);
        }

        $data = $response->json();

        return response()->json([
    'options' => [
        'product_id' => (int) env('VITE_FREEMIUS_PRODUCT_ID'),
        'public_key' => env('VITE_FREEMIUS_PUBLIC_KEY'),
    ],
    'plans' => [
        [
            'id' => '12345', // Plan ID
            'title' => 'Pro Plan',
            'description' => 'Best for individuals',
            'is_featured' => true,
            'pricing' => [
                [
                    'id' => '67890', // Pricing ID
                    'currency' => 'usd',
                    'is_hidden' => false,
                    'monthly_price' => 19.99, // Required for the React filter
                    'annual_price' => 199.99,  // Required for the React filter
                ]
            ],
            'features' => [
                ['title' => 'Feature 1', 'value' => 'Included'],
                ['title' => 'Feature 2'],
            ]
        ]
    ]
]);
    }
}
