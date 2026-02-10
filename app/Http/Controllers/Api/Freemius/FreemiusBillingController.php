<?php

namespace App\Http\Controllers\Api\Freemius;

use App\Http\Controllers\Controller;
use App\Models\Freemius\FreemiusBilling;
use App\Services\Freemius\FreemiusBillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class FreemiusBillingController extends Controller
{
    protected $freemiusBillingService;

    public function __construct(FreemiusBillingService $freemiusBillingService)
    {
        $this->freemiusBillingService = $freemiusBillingService;
    }

    // In FreemiusBillingController.php

    public function showByFsUserId($fs_user_id)
    {
        $billing = FreemiusBilling::where('fs_user_id', $fs_user_id)->first();

        return response()->json($billing);
    }

    public function updateByFsUserId(Request $request, $fs_user_id)
    {
        if ((string) $fs_user_id !== (string) (auth()->user()->subscription->fs_user_id ?? null)) {
            abort(403, 'Unauthorized: You do not own this billing record.');
        }

        $data = $request->validate([
            'fs_user_id' => ['nullable', 'integer', Rule::unique('freemius_billings')->ignore($fs_user_id)],
            'business_name' => 'nullable|string',
            'first' => 'nullable|string',
            'last' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'website' => 'nullable|url',
            'tax_id' => 'nullable|string',
            'address_street' => 'nullable|string',
            'address_apt' => 'nullable|string',
            'address_city' => 'nullable|string',
            'address_state' => 'nullable|string',
            'address_zip' => 'nullable|string',
            'address_country' => 'nullable|string',
            'address_country_code' => 'nullable|string|max:10',
            'fs_created_at' => 'nullable|date',
            'fs_updated_at' => 'nullable|date',
        ]);

        $billing = $this->freemiusBillingService->updateByFsUserId($data, $fs_user_id);

        // 4. Update Freemius API
        // Replace {product_id} with your actual Freemius Product ID
        $productId = config('freemius.product_id');
        $headers = ['Authorization' => 'Bearer '.config('freemius.bearer_token')];

        $response = Http::withHeaders($headers)
            ->put("https://api.freemius.com/v1/products/{$productId}/users/{$fs_user_id}/billing.json", $data);

        if ($response->failed()) {
            // Handle API failure (optional: log it or throw error)
            logger()->error('Freemius Sync Failed', $response->json());
        }

        return $billing;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $billing = FreemiusBilling::findOrFail($id);
        $billing->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
