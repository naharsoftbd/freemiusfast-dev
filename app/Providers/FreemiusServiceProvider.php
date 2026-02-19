<?php

namespace App\Providers;

use App\Interfaces\Freemius\FreemiusBillingRepositoryInterface;
use App\Interfaces\Freemius\FreemiusProductRepositoryInterface;
use App\Repositories\Freemius\FreemiusBillingRepository;
use App\Repositories\Freemius\FreemiusProductRepository;
use Illuminate\Support\ServiceProvider;

class FreemiusServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(FreemiusBillingRepositoryInterface::class, FreemiusBillingRepository::class);
        $this->app->bind(FreemiusProductRepositoryInterface::class, FreemiusProductRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
