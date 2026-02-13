<?php

namespace App\Http\Controllers\Freemius;

use App\Http\Controllers\Controller;
use App\Models\Freemius\Subscription;
use App\Services\Freemius\FreemiusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FreemiusPaymentController extends Controller
{
    protected $freemiusService;

    public function __construct(FreemiusService $freemiusService)
    {
        $this->freemiusService = $freemiusService;
    }

    public function paymentSuccess(Request $request)
    {
        // 1. Verify the Signature
        $protocol = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $current_url = $protocol.'://'.$host.$_SERVER['REQUEST_URI'];

        // Remove the "&signature=..." part using string slicing
        $signature_pos = strpos($current_url, '&signature=');
        $clean_url = substr($current_url, 0, $signature_pos);

        $receivedSignature = $request->input('signature');

        if (! $this->freemiusService->isSignatureValid($clean_url, $receivedSignature)) {
            Log::warning('Unauthorized Freemius attempt detected.', ['$clean_url' => $clean_url]);

            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // 2️⃣ Validate input
        $data = Validator::validate($request->all(), [
            'user_id' => 'required|integer',
            'action' => 'required|string',
            'amount' => 'required|numeric',
            'billing_cycle' => 'nullable|integer',
            'currency' => 'required|string|size:3',
            'email' => 'required|email',
            'expiration' => 'nullable|date',
            'license_id' => 'required|string',
            'plan_id' => 'required|integer',
            'pricing_id' => 'required|integer',
            'quota' => 'required|integer',
            'subscription_id' => 'nullable|integer',
            'payment_id' => 'nullable|integer',
            'signature' => 'required|string',
            'tax' => 'required|numeric',
        ]);

        // 3️⃣ Map Freemius user → local user
        $data['fs_user_id'] = $data['user_id'];
        
        // User Id Removed for Hosted checkout
        unset($data['user_id']);
        //$data['user_id'] = auth()->id(); 

        // 4. Idempotency (VERY IMPORTANT)
        $subscription = Subscription::firstOrCreate(
            $data
        );

        // 5. Redirect user (NOT JSON)
        return redirect()
            ->route('portal.account')
            ->with('success', 'Payment completed successfully 🎉');
    }
}
