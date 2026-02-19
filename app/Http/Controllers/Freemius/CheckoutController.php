<?php

namespace App\Http\Controllers\Freemius;

use App\Http\Controllers\Controller;
use App\Services\Freemius\FreemiusService;
use App\Traits\FreemiusConfigTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    use FreemiusConfigTrait;

    protected $freemiusService;

    public function __construct(FreemiusService $freemiusService)
    {
        $this->initFreemius(); // initialize shared Freemius config
        $this->freemiusService = $freemiusService;
    }

    public function apiCheckout(Request $request)
    {
        $plans = $this->freemiusService->checkout();

        return response()->json([
            'options' => [
                'product_id' => (int) $this->productId,
                'public_key' => $this->publicKey,
            ],
            'plans' => $plans,
        ]);
    }

    public function checkout(Request $request)
    {
        return Inertia::render('Freemius/PricingPage');
    }

    public function developer(Request $request)
    {
        $devId = '27811';
        $publicKey = 'pk_f6af2f65c61bd27a936fcb11b4103';
        $secretKey = 'sk_6w1[0-(Ly8AHM]z>@UB:?Y(h<>2<C';

        $api = new \Freemius_Api('developer', $devId, $publicKey, $secretKey);
        $response = $api->Api('/plugins.json');
        $first_plugin_id = $response->plugins[0]->id;
        $first_plugin = $api->Api("/plugins/{$first_plugin_id}.json", 'PUT', [
            'title' => 'Demo Product',
            'accepted_payments' => 0,
        ]);
        $resutls = $api->Api("/plugins/{$first_plugin_id}/payments.json");

        return response()->json($first_plugin);
        // return Inertia::render('Freemius/PricingPage');
    }
}
