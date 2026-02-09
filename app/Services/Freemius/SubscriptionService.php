<?php

namespace App\Services\Freemius;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SubscriptionService
{
    protected  $accessToken;

    protected  $secretKey;

    protected  $productId;

    protected  $planId;

    protected  $headers;

    protected  $apiBaseUrl;

    protected  $publicKey;

    protected  $baseUrl;

    protected  $fsUserId;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->accessToken = config('freemius.bearer_token');
        $this->secretKey = config('freemius.secret_key');
        $this->productId = config('freemius.product_id');
        $this->headers = ['Authorization' => 'Bearer '.config('freemius.bearer_token')];
        $this->apiBaseUrl = config('freemius.api_base_url');
        $this->publicKey = config('freemius.public_key');
        $this->baseUrl = "{$this->apiBaseUrl}/{$this->productId}";
        $this->fsUserId = User::find(auth()->id())->subscription->fs_user_id;
    }

    //Subscription Cancel
    public function cancelSubscription(int|string $subscriptionId, ?string $reason = null, array $reasonIds = [])
    {
        $response = Http::withHeaders($this->headers)
            ->delete(
                "{$this->baseUrl}/subscriptions/{$subscriptionId}.json",
                [
                    'reason' => $reason,
                    'reason_ids' => $reasonIds,
                ]
            );

        return $response->json();
    }
}
