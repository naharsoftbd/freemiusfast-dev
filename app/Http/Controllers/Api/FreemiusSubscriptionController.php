<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApiResponseService;
use App\Services\Freemius\SubscriptionService;
use Illuminate\Http\Request;

class FreemiusSubscriptionController extends Controller
{
    protected $freemiusSubscriptionService;

    protected $apiResponseService;

    public function __construct(SubscriptionService $freemiusSubscriptionService, ApiResponseService $apiResponseService)
    {
        $this->freemiusSubscriptionService = $freemiusSubscriptionService;
        $this->apiResponseService = $apiResponseService;
    }

    public function cancelSubscription(Request $request, $subscriptionId)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string',
            'reason_ids' => 'nullable|array',
            'reason_ids.*' => 'integer',
        ]);

        $response = $this->freemiusSubscriptionService->cancelSubscription(
            subscriptionId: $subscriptionId,
            reason: $validated['reason'] ?? 'User requested cancellation',
            reasonIds: $validated['reason_ids'] ?? []
        );

        return ApiResponseService::success($response, 'Subscription cancelled successfully');
    }
}
