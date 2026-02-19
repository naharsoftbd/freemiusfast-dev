<?php

namespace App\Http\Controllers\Api\Freemius;

use App\Http\Controllers\Controller;
use App\Models\Freemius\Subscription;
use App\Models\FreemiusLicense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FreemiusWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Log the incoming data as JSON for easy reading in storage/logs/laravel.log
        Log::info('Freemius Webhook Received:', $request->all());
        // 1. Verify the Request (Optional: Freemius provides a signature check)
        $payload = $request->input('objects'); // Freemius often wraps data in 'objects' or 'license'

        // If the webhook is specifically for a license
        $licenseData = $request->input('license') ?? $request->input('objects.license') ?? $request->all();

        // Check if we actually have an ID to work with
        if (! isset($licenseData['id'])) {
            Log::warning('Freemius Webhook missing ID', $licenseData);

            return response()->json(['status' => 'error', 'message' => 'No ID found'], 400);
        }

        $user_id = Subscription::where('fs_uiser_id', $licenseData['user_id'])->first()?->id ?? null;

        try {
            // 2. Use updateOrCreate to stay in sync
            FreemiusLicense::updateOrCreate(
                ['freemius_id' => $licenseData['id']],
                [
                    'fs_user_id' => $licenseData['user_id'] ?? null,
                    'product_id' => $licenseData['plugin_id'] ?? null,
                    'user_id' => $user_id,
                    'plan_id' => $licenseData['plan_id'] ?? null,
                    'pricing_id' => $licenseData['pricing_id'] ?? null,
                    'quota' => $licenseData['quota'] ?? 0,
                    'activated' => $licenseData['activated'] ?? 0,
                    'activated_local' => $licenseData['activated_local'] ?? 0,
                    'expiration' => $licenseData['expiration'] ?? null,
                    'secret_key' => $licenseData['secret_key'] ?? '',
                    'plugin_type' => $licenseData['plugin_type'] ?? '',
                    'is_free_localhost' => $licenseData['is_free_localhost'] ?? true,
                    'is_block_features' => $licenseData['is_block_features'] ?? false,
                    'is_cancelled' => $licenseData['is_cancelled'] ?? false,
                    'is_whitelabeled' => $licenseData['is_whitelabeled'] ?? true,
                    'environment' => $licenseData['environment'] ?? 0,
                    'source' => $licenseData['source'] ?? 0,
                    'products' => $licenseData['products'] ?? [],
                    'expiration' => $licenseData['expiration'] ?? null,
                    'freemius_created_at' => $licenseData['created'] ?? now(),
                    'freemius_updated_at' => $licenseData['updated'] ?? now(),
                    // Add other fields as needed
                ]
            );

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Freemius Webhook Error: '.$e->getMessage());

            return response()->json(['status' => 'error'], 500);
        }
    }
}
