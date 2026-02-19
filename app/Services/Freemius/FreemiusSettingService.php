<?php

namespace App\Services\Freemius;

use App\Http\Resources\Freemius\FreemiusProductResource;
use App\Jobs\Freemius\SyncFreemiusProductSetting;
use App\Models\Freemius\FreemiusFeature;
use App\Models\Freemius\FreemiusPlan;
use App\Models\Freemius\FreemiusPricing;
use App\Models\Freemius\FreemiusSetting;
use App\Traits\FreemiusConfigTrait;

class FreemiusSettingService
{
    use FreemiusConfigTrait;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->initFreemius(); // initialize shared Freemius config
    }

    public function getFreemiusProductSetting()
    {
        $setting = FreemiusSetting::first();

        if (! $setting) {
            return null;
        }

        return new FreemiusProductResource($setting);
    }

    public function setFreemiusProductSetting(array $data): FreemiusSetting
    {
        $product = FreemiusSetting::updateOrCreate(
            // 1️⃣ Find by unique identifier ONLY
            [
                'freemius_product_id' => $data['freemius_product_id'],
            ],
            [
                'developer_id' => $data['developer_id'],
                'developer_public_key' => $data['developer_public_key'],
                'developer_secret_key' => $data['developer_secret_key'],
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
                // 'is_synced' => false,
                'user_id' => $data['user_id'],
                'public_url' => $data['public_url'], // Your Business Url
                'base_url' => $data['base_url'], // Freemius Base Url
                'api_base_url' => $data['api_base_url'], // Freemius API Base Url
            ]);
        // Dispatch Job
        SyncFreemiusProductSetting::dispatch($product);

        return $product;

    }

    public function syncProductSetting(FreemiusSetting $product)
    {
        // 1. Fetch data from Freemius API
        // Note: Freemius often requires a custom signature.
        // If you are using the new Bearer token, use withToken().

        // FIRST TIME SYNC (Not synced yet)
        if (! $product->is_synced) {

            $response = $this->client()->get(
                "{$this->apiBaseUrl}/{$product->freemius_product_id}.json"
            );
            $data = $response->json();

        } else {
            // UPDATE

            $api = $this->getFreemiusApi();
            $data = $api->Api("/plugins/{$product->freemius_product_id}.json", 'PUT', $this->transformProduct($product));
            $data = (array) $data;

        }

        // 2. product Sync to Database
        FreemiusSetting::updateOrCreate(
            ['freemius_product_id' => $data['id']], // Unique identifier
            [
                'title' => $data['title'],
                'slug' => $data['slug'],
                'icon' => $data['icon'],
                'secret_key' => $data['secret_key'],
                'public_key' => $data['public_key'],
                'type' => $data['type'],
                'money_back_period' => $data['money_back_period'],
                'refund_policy' => $data['refund_policy'] ?? 'strict',
                'annual_renewals_discount' => $data['annual_renewals_discount'] ?? 0,
                'renewals_discount_type' => $data['renewals_discount_type'],
                'lifetime_license_proration_days' => $data['lifetime_license_proration_days'],
                'is_pricing_visible' => $data['is_pricing_visible'],
                'accepted_payments' => $data['accepted_payments'],
                // Map other fields here...
                'freemius_payload' => $data,
                'is_synced' => true,
            ]
        );

        $plansResponse = $this->client()->get("{$this->baseUrl}/plans.json");

        $plans = $plansResponse->json('plans');

        foreach ($plans as &$plan) {
            FreemiusPlan::updateOrCreate(
                ['plan_id' => $plan['id']],
                [
                    'plugin_id' => $plan['plugin_id'],
                    'name' => $plan['name'],
                    'title' => $plan['title'],
                    'description' => $plan['description'],
                    'is_free_localhost' => $plan['is_free_localhost'],
                    'is_block_features' => $plan['is_block_features'],
                    'is_block_features_monthly' => $plan['is_block_features_monthly'],
                    'license_type' => $plan['license_type'],
                    'is_https_support' => $plan['is_https_support'],
                    'trial_period' => $plan['trial_period'],
                    'is_require_subscription' => $plan['is_require_subscription'],
                    'support_kb' => $plan['support_kb'],
                    'support_forum' => $plan['support_forum'],
                    'support_email' => $plan['support_email'],
                    'support_phone' => $plan['support_phone'],
                    'support_skype' => $plan['support_skype'],
                    'is_success_manager' => $plan['is_success_manager'],
                    'is_featured' => $plan['is_featured'],
                    'is_hidden' => $plan['is_hidden'],
                    'freemius_created_at' => $plan['created'],
                    'freemius_updated_at' => $plan['updated'],
                ]
            );

            // Plan Pricing
            $pricing = $this->client()
                ->get("{$this->baseUrl}/plans/{$plan['id']}/pricing.json")
                ->json('pricing');

            $plan['pricing'] = collect($pricing)->map(function ($price) {
                return [
                    ...$price,
                    'currency' => $price['currency'],
                ];
            })->values()->all();

            foreach ($plan['pricing'] as $pricing) {
                FreemiusPricing::updateOrCreate(
                    ['pricing_id' => $pricing['id']],
                    [
                        'plan_id' => $pricing['plan_id'],
                        'licenses' => $pricing['licenses'],
                        'monthly_price' => $pricing['monthly_price'],
                        'annual_price' => $pricing['annual_price'],
                        'lifetime_price' => $pricing['lifetime_price'],
                        'currency' => $pricing['currency'],
                        'is_whitelabeled' => $pricing['is_whitelabeled'],
                        'is_hidden' => $pricing['is_hidden'],
                        'freemius_created_at' => $pricing['created'],
                        'freemius_updated_at' => $pricing['updated'],
                    ]
                );
            }

            // ✅ Get Features dynamically
            $featuresResponse = $this->client()->get("{$this->baseUrl}/plans/{$plan['id']}/features.json");

            $features = $featuresResponse->json('features');

            $plan['features'] = collect($features)->map(function ($feature) {
                return [
                    'id' => $feature['id'],
                    'plan_id' => $feature['plan_id'],
                    'title' => $feature['title'],
                    'description' => $feature['description'],
                    'is_featured' => $feature['is_featured'],
                    'value' => $feature['value'],
                ];
            })->values()->all();

            foreach ($plan['features'] as $feature) {
                // 1️⃣ Create or get UNIQUE feature (by title)
                $featureModel = FreemiusFeature::updateOrCreate(
                    [
                        'feature_id' => $feature['id'],
                    ],
                    [
                        'title' => $feature['title'], // Unique key
                    ],
                    [
                        'description' => $feature['description'],
                    ]
                );

                // 2️⃣ Attach to plan with pivot data
                $planModel = FreemiusPlan::where('plan_id', $plan['id'])->first();

                $planModel->features()->syncWithoutDetaching([
                    $featureModel->id => [
                        'value' => $feature['value'],
                        'is_featured' => $feature['is_featured'],
                    ]
                ]);
            }

        }

        unset($plan);
    }

    private function transformProduct(FreemiusSetting $product): array
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
