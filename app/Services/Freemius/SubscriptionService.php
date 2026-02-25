<?php

namespace App\Services\Freemius;

use App\Jobs\Freemius\SyncFreemiusCustomerData;
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

    // Subscription Cancel
    public function cancelSubscription(int|string $subscriptionId, ?string $reason = null, array $reasonIds = [])
    {
        $response = $this->client()
            ->delete(
                "{$this->baseUrl}/subscriptions/{$subscriptionId}.json",
                [
                    'reason'     => $reason,
                    'reason_ids' => $reasonIds,
                ]
            );
        SyncFreemiusCustomerData::dispatch(auth()->user());

        return $response->json();
    }
}
