<?php

namespace App\Services\Freemius;

use App\Models\Freemius\FreemiusProduct;
use App\Traits\FreemiusConfigTrait;
use App\Jobs\SyncFreemiusProduct;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\Freemius\FreemiusProductResource;

class FreemiusProductService
{
    use FreemiusConfigTrait;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->initFreemius(); // initialize shared Freemius config
    }

    public function getProducts($perPage, $search)
    {
        $products = FreemiusProduct::query();

        if ($search) {
            $products->where(
                fn ($query) => $query->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
            );
        }

        $products = $products->latest()->paginate($perPage)->withQueryString();

        $products->getCollection()->transform(fn ($product) =>
                        [
                        'id' => $product->id,
                        'title' => $product->title ?? null,
                        'slug' => $product->slug ?? null,
                        'type' => $product->type ?? null,
                        'api_toke' => $product->api_toke ?? null,
                        'icon' => $product->icon ?? null,
                        'money_back_period' => $product->money_back_period ?? null,
                        'refund_policy' => $product->refund_policy ?? null,
                        'annual_renewals_discount' => $product->annual_renewals_discount ?? null,
                        'renewals_discount_type' => $product->renewals_discount_type ?? null,
                        'lifetime_license_proration_days' => $product->lifetime_license_proration_days ?? null,
                        'is_pricing_visible' => $product->is_pricing_visible ?? false,
                        'accepted_payments' => $product->accepted_payments ?? null,
                        'expose_license_key' => $product->expose_license_key ?? false,
                        'enable_after_purchase_email_login_link' => $product->enable_after_purchase_email_login_link ?? false,
                        'is_synced' => $product->is_synced,
                    ]);

        return $products;
    }

    public function getProduct($id)
    {
        $product = FreemiusProduct::findOrFail($id);

        return new FreemiusProductResource($product);
    }

    public function create(array $data): FreemiusProduct
    {
        $product = FreemiusProduct::create([
            'freemius_product_id' => $data['freemius_product_id'],
            'api_token' => $data['api_token'],
            'title' => $data['title'],
            'slug' => $data['slug'] ?? null,
            'type' => $data['type'] ?? null,
            'icon' => $data['icon'] ?? null,
            'money_back_period' => $data['money_back_period'] ?? null,
            'refund_policy' => $data['refund_policy'] ?? null,
            'annual_renewals_discount' => $data['annual_renewals_discount'] ?? null,
            'renewals_discount_type' => $data['renewals_discount_type'] ?? null,
            'lifetime_license_proration_days' => $data['lifetime_license_proration_days'] ?? null,
            'is_pricing_visible' => $data['is_pricing_visible'],
            'accepted_payments' => $data['accepted_payments'],
            'expose_license_key' => $data['expose_license_key'],
            'enable_after_purchase_email_login_link' => $data['enable_after_purchase_email_login_link'],
            'freemius_payload' => $data['freemius_payload'] ?? null,
            'is_synced' => false,
            'user_id' => $data['user_id']
        ]);
        // Dispatch Job
        SyncFreemiusProduct::dispatch($product);

        return $product;
    }

    public function updateProduct(string $productId, array $data)
    {
        $product = FreemiusProduct::findOrFail($productId);

        $product->update([
            'freemius_product_id' => $data['freemius_product_id'],
            'api_token' => $data['api_token'],
            'title' => $data['title'],
            'slug' => $data['slug'] ?? null,
            'type' => $data['type'] ?? null,
            'icon' => $data['icon'] ?? null,
            'money_back_period' => $data['money_back_period'] ?? null,
            'refund_policy' => $data['refund_policy'] ?? null,
            'annual_renewals_discount' => $data['annual_renewals_discount'] ?? null,
            'renewals_discount_type' => $data['renewals_discount_type'] ?? null,
            'lifetime_license_proration_days' => $data['lifetime_license_proration_days'] ?? null,
            'is_pricing_visible' => $data['is_pricing_visible'],
            'accepted_payments' => $data['accepted_payments'],
            'expose_license_key' => $data['expose_license_key'],
            'enable_after_purchase_email_login_link' => $data['enable_after_purchase_email_login_link'],
            'freemius_payload' => $data['freemius_payload'] ?? null,
            'is_synced' => false,
        ]);

        // Dispatch Job
        SyncFreemiusProduct::dispatch($product, true);

        return $product;
    }

    public function syncProduct($productId)
    {
        // 1. Fetch data from Freemius API
        // Note: Freemius often requires a custom signature. 
        // If you are using the new Bearer token, use withToken().

        Log::warning('Unauthorized Freemius attempt detected.', ['apiBaseUrl' => $this->apiBaseUrl]);
        
        $response = $this->client()->get("{$this->apiBaseUrl}/{$productId}.json");        
        

        if ($response->failed()) {
            throw new \Exception("Freemius API error: " . $response->body());
        }

        $data = $response->json();

        // 2. Sync to Database
        return FreemiusProduct::updateOrCreate(
            ['freemius_product_id' => $data['id']], // Unique identifier
            [
                'title' => $data['title'],
                'slug'  => $data['slug'],
                'secret_key'  => $data['secret_key'],
                'public_key'  => $data['public_key'],
                'type' => $data['type'],
                'money_back_period' => $data['money_back_period'],
                'refund_policy' => $data['refund_policy'] ?? 'strict',
                'annual_renewals_discount' => $data['annual_renewals_discount'] ?? 0,
                'renewals_discount_type' => $data['renewals_discount_type'],
                'lifetime_license_proration_days' => $data['lifetime_license_proration_days'],
                'is_pricing_visible' => $data['is_pricing_visible'],
                // Map other fields here...
                'freemius_payload' => $data,
                'is_synced' => true,
            ]
        );
    }

    /**
     * Update product in Freemius API
     */
    public function sysncUpdateProduct(FreemiusProduct $product)
    {
        $this->client()->put(
            "{$this->apiBaseUrl}/{$product->freemius_product_id}.json",
            $this->transformProduct($product)
        );

        if ($response->failed()) {
            throw new \Exception("Freemius API error: " . $response->body());
        }

        $data = $response->json();

        // 2. Sync to Database
        return FreemiusProduct::updateOrCreate(
            ['freemius_product_id' => $data['id']], // Unique identifier
            [
                'title' => $data['title'],
                'slug'  => $data['slug'],
                'type' => $data['type'],
                'money_back_period' => $data['money_back_period'],
                'refund_policy' => $data['refund_policy'] ?? 'strict',
                'annual_renewals_discount' => $data['annual_renewals_discount'] ?? 0,
                'renewals_discount_type' => $data['renewals_discount_type'],
                'lifetime_license_proration_days' => $data['lifetime_license_proration_days'],
                'is_pricing_visible' => $data['is_pricing_visible'],
                // Map other fields here...
                'freemius_payload' => $data,
                'is_synced' => true,
            ]
        );
    }

    private function transformProduct(FreemiusProduct $product): array
    {
        return [
            'title' => $product->title,
            'slug' => $product->slug,
            'type' => $product->type,
            'money_back_period' => $product->money_back_period,
            'refund_policy' => $product->refund_policy,
            'annual_renewals_discount' => $product->annual_renewals_discount,
            'renewals_discount_type' => $product->renewals_discount_type,
            'lifetime_license_proration_days' => $product->lifetime_license_proration_days,
            'is_pricing_visible' => $product->is_pricing_visible,
            'accepted_payments' => $product->accepted_payments,
            'expose_license_key' => $product->expose_license_key,
            'enable_after_purchase_email_login_link' => $product->enable_after_purchase_email_login_link,
        ];
    }
}
