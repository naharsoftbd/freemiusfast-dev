<?php

namespace App\Providers;

use App\Interfaces\Freemius\FreemiusBillingRepositoryInterface;
use App\Repositories\Freemius\FreemiusBillingRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            FreemiusBillingRepositoryInterface::class,
            FreemiusBillingRepository::class
        );
    }
}
