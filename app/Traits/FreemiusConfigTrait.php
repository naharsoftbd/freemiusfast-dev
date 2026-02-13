<?php

namespace App\Traits;

use App\Models\Freemius\Subscription;
use Illuminate\Support\Facades\Http;

trait FreemiusConfigTrait
{
    protected string $accessToken;

    protected string $secretKey;

    protected string $productId;

    protected array $headers;

    protected string $apiBaseUrl;

    protected string $publicKey;

    protected string $baseUrl;

    protected function initFreemius(): void
    {
        $this->accessToken = config('freemius.bearer_token');
        $this->secretKey = config('freemius.secret_key');
        $this->productId = config('freemius.product_id');
        $this->headers = ['Authorization' => 'Bearer '.$this->accessToken];
        $this->apiBaseUrl = config('freemius.api_base_url');
        $this->publicKey = config('freemius.public_key');
        $this->baseUrl = "{$this->apiBaseUrl}/{$this->productId}";
    }

    protected function getFsUserId(): ?string
    {
        $user = auth()->user();
        
        if (!$user) {
            return null;
        }

        $subscription = Subscription::where('email', $user->email)->first();

        return $subscription ? $subscription->fs_user_id : null;

        // Removed for Hosted Checkout
        //return $user?->subscription?->fs_user_id ?? null;
    }

    protected function client()
    {
        return Http::withHeaders($this->headers)
            ->timeout(15)
            ->retry(3, 200);
    }

    protected function getBaseUrl()
    {   
        $baseUrl = "{$this->apiBaseUrl}/{$this->productId}";
        
        return $baseUrl;
    }
}
