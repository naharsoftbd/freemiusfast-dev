<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Freemius\FreemiusSetting;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Wrap all DB interactions safely
        try {
            // Freemius Product Setting
            if (Schema::hasTable('freemius_settings')) {
                Inertia::share('freemius', function () {
                    $settings = FreemiusSetting::first();

                    return [
                        'product_id' => $settings->freemius_product_id ?? null,
                        'public_key' => $settings->public_key ?? null,
                        'secret_key' => $settings->secret_key ?? null,
                        'bearer_token' => $settings->api_token ?? null,
                        'base_url' => $settings->base_url ?? null,
                        'public_url' => $settings->public_url ?? null,
                        'api_base_url' => $settings->api_base_url ?? null,
                    ];
                });
            } else {
                Inertia::share('freemius', []);
                View::share('freemius', []);
            }
        } catch (\Throwable $e) {
            // Database not ready — likely during first install
            Inertia::share('freemius', []);
            View::share('freemius', []);
        }
        Vite::prefetch(concurrency: 3);
    }
}
