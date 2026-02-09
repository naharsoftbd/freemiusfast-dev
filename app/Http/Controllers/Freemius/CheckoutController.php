<?php

namespace App\Http\Controllers\Freemius;

use App\Http\Controllers\Controller;
use App\Services\Freemius\FreemiusService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected $freemiusService;

    public function __construct(FreemiusService $freemiusService)
    {
        $this->freemiusService = $freemiusService;
    }

    public function checkout(Request $request)
    {
        $plans = $this->freemiusService->checkout();

        return response()->json([
            'options' => [
                'product_id' => (int) env('VITE_FREEMIUS_PRODUCT_ID'),
                'public_key' => env('VITE_FREEMIUS_PUBLIC_KEY'),
            ],
            'plans' => $plans,
        ]);
    }
}
