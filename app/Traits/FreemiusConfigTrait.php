<?php

namespace App\Traits;

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

        return $user?->subscription?->fs_user_id ?? null;
    }
}
