<?php

namespace App\Http\Controllers\Freemius;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Freemius\Subscription;
use Illuminate\Support\Facades\Log;
use App\Services\Freemius\FreemiusService;
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
        // if (!$this->isSignatureValid($data, $receivedSignature)) {
        //     Log::warning('Unauthorized Freemius attempt detected.', ['data' => $data]);
        //     return response()->json(['error' => 'Invalid signature'], 401);
        // }

        // 2️⃣ Validate input
        $data = Validator::validate($request->all(), [
            'user_id'      => 'required|integer',
            'action'       => 'required|string',
            'amount'       => 'required|numeric',
            'billing_cycle'=> 'nullable|integer',
            'currency'     => 'required|string|size:3',
            'email'        => 'required|email',
            'expiration'   => 'nullable|date',
            'license_id'   => 'required|string',
            'plan_id'      => 'required|integer',
            'pricing_id'   => 'required|integer',
            'quota'        => 'required|integer',
            'subscription_id' => 'nullable|integer',
            'payment_id'   => 'required|integer',
            'signature'    => 'required|string',
            'tax'          => 'required|numeric',
        ]);

        // 3️⃣ Map Freemius user → local user
        $data['fs_user_id'] = $data['user_id'];
        $data['user_id'] = auth()->id();

        // 4. Idempotency (VERY IMPORTANT)
        $subscription = Subscription::firstOrCreate(
            ['payment_id' => $data['payment_id']],
            $data
        );

        // 5. Redirect user (NOT JSON)
        return redirect()
            ->route('portal.account')
            ->with('success', 'Payment completed successfully 🎉');
    }
}
