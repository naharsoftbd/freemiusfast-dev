<?php

namespace App\Http\Controllers\Freemius;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Freemius\Subscription;
use App\Http\Requests\Freemius\FreemiusPaymentSuccessRequest;
use Illuminate\Support\Facades\Log;
use App\Services\Freemius\FreemiusService;

class FreemiusPaymentController extends Controller
{
    protected $freemiusService;

    public function __construct(FreemiusService $freemiusService)
    {
        $this->freemiusService = $freemiusService;
    }

    public function paymentSuccess(FreemiusPaymentSuccessRequest $request)
    {
        $data = $request->valideted();
        $receivedSignature = $request->input('signature');

        // 1. Verify the Signature
        if (!$this->isSignatureValid($data, $receivedSignature)) {
            Log::warning('Unauthorized Freemius attempt detected.', ['data' => $data]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // 2. Map data to your fs_user_id column
        if (isset($data['user_id'])) {
            $data['fs_user_id'] = $data['user_id'];
            $data['user_id'] = auth()->id(); // Remove the original key so it doesn't conflict
        }

        // 3. Insert dynamically (excluding user_id if it's not in your payload)
        $subscription = Subscription::create($data);

        return response()->json([
            'message' => 'Subscription updated successfully',
            'data' => $subscription
        ]);
    }
}
