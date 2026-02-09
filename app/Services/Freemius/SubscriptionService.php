<?php

namespace App\Services\Freemius;

use Illuminate\Support\Facades\Http;
use App\Traits\FreemiusConfigTrait;

class SubscriptionService
{
    use FreemiusConfigTrait;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->initFreemius(); // initialize shared Freemius config
    }

    //Subscription Cancel
    public function cancelSubscription(int|string $subscriptionId, ?string $reason = null, array $reasonIds = [])
    {
        $response = Http::withHeaders($this->headers)
            ->delete(
                "{$this->baseUrl}/subscriptions/{$subscriptionId}.json",
                [
                    'reason' => $reason,
                    'reason_ids' => $reasonIds,
                ]
            );

        return $response->json();
    }
}
