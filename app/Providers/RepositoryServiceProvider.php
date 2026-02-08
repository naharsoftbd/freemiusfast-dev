<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\Freemius\FreemiusBillingRepositoryInterface;
use App\Repositories\Freemius\FreemiusBillingRepository;

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
