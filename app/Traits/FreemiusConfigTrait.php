<?php

namespace App\Traits;

use App\Models\Freemius\FreemiusSetting;
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

    protected string $developerId;

    protected string $developerPublicKey;

    protected string $developerSecretKey;

    protected string $publicUrl;

    protected function initFreemius(): void
    {
        $product = FreemiusSetting::first();

        $this->accessToken = $product->api_token ?? '';
        $this->productId = $product->freemius_product_id ?? '';
        $this->secretKey = $product->secret_key ?? '';
        $this->publicKey = $product->public_key ?? '';
        $this->headers = ['Authorization' => 'Bearer '.$this->accessToken];
        $this->apiBaseUrl = $product->api_base_url ?? '';
        $this->baseUrl = $this->getBaseUrl();
        $this->developerId = $product->developer_id ?? '';
        $this->developerPublicKey = $product->developer_public_key ?? '';
        $this->developerSecretKey = $product->developer_secret_key ?? '';
        $this->publicUrl = $product->public_url ?? '';
    }

    protected function getFsUserId(): ?string
    {
        $user = auth()->user();

        return $user?->entitlement?->fs_user_id ?? null;
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

    protected function getFreemiusApi()
    {
        $api = new \Freemius_Api('developer', $this->developerId, $this->developerPublicKey, $this->developerSecretKey);

        return $api;
    }
}
