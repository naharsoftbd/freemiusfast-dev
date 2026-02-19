<?php

namespace App\Http\Controllers\Freemius;

use App\Enums\RefundPolicy;
use App\Http\Controllers\Controller;
use App\Services\Freemius\FreemiusSettingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class FreemiusSettingController extends Controller
{
    protected $freemiusSettingService;

    public function __construct(FreemiusSettingService $freemiusSettingService)
    {
        $this->freemiusSettingService = $freemiusSettingService;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function getFreemiusProductSetting()
    {
        $setting = $this->freemiusSettingService->getFreemiusProductSetting();

        if ($setting) {
            return Inertia::render('Freemius/Settings/Edit', [
                'setting' => $setting,
            ]);
        }

        return Inertia::render('Freemius/Settings/Create');

    }

    public function setFreemiusProductSetting(Request $request)
    {
        $setting = $this->freemiusSettingService->getFreemiusProductSetting();

        $validated = $request->validate([
            'developer_id' => ['required', 'integer', 'min:0'],
            'developer_public_key' => ['required', 'string', 'max:255'],
            'developer_secret_key' => ['required', 'string', 'max:255'],
            'freemius_product_id' => ['required', 'integer', 'min:0', Rule::unique('freemius_settings', 'freemius_product_id')
                ->ignore($setting?->id)],
            'api_token' => ['required', 'string', 'min:0', Rule::unique('freemius_settings', 'api_token')
                ->ignore($setting?->id)],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('freemius_settings', 'slug')
                ->ignore($setting?->id)],
            'type' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string'],
            'money_back_period' => ['required', 'integer', 'min:0'],
            'refund_policy' => ['required', Rule::in(RefundPolicy::values())],
            'annual_renewals_discount' => ['required', 'integer', 'min:0'],
            'renewals_discount_type' => ['required', 'in:percentage,fixed'],
            'lifetime_license_proration_days' => ['required', 'integer', 'min:0'],
            'is_pricing_visible' => ['boolean'],
            'accepted_payments' => ['required', 'integer', 'min:0'],
            'expose_license_key' => ['boolean'],
            'enable_after_purchase_email_login_link' => ['boolean'],
            'public_url' => ['required', 'string', 'max:255'], // Your Business Url
            'base_url' => ['required', 'string', 'max:255'], // Freemius Base Url
            'api_base_url' => ['required', 'string', 'max:255'], // Freemius API Base Url
        ]);

        $product = $this->freemiusSettingService->setFreemiusProductSetting([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Settings saved successfully.');

    }
}
