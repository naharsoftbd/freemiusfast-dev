<?php

namespace App\Services\Freemius;

use App\Traits\FreemiusConfigTrait;
use App\Models\Freemius\FreemiusLicense;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Freemius\Subscription;

class FreemiusLicenseService
{
    use FreemiusConfigTrait;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->initFreemius();
    }

    public function getLicenses()
    {
        return FreemiusLicense::all();  
    }

    public function getLicenseByUser($userId)
    {
        $user = User::find($userId);
        $fsUserId = Subscription::where('email', $user->email)->first()->fs_user_id;
       return FreemiusLicense::where('fs_user_id', $fsUserId)->get();  
    }

    public function syncUserLicenses($userId): void
    {
        $user = User::find($userId);
        $fsUserId = Subscription::where('email', $user->email)->first()->fs_user_id;
        $response = $this->client()
            ->get("{$this->baseUrl}/users/{$fsUserId}/licenses.json");

        if ($response->failed()) {
            throw new \Exception('Failed to fetch licenses');
        }

        Log::warning('licenses attempt detected.', ['response' => $response]);

        $licenses = $response->json('licenses') ?? [];
        
        foreach ($licenses as $license) {
            FreemiusLicense::updateOrCreate(
                [
                    'freemius_id' => $license['id'], // unique key
                ],
                [
                    'user_id'           => $userId,
                    'fs_user_id'         => $license['user_id'],
                    'product_id'         => $license['plugin_id'],
                    'plan_id'            => $license['plan_id'],
                    'pricing_id'         => $license['pricing_id'],
                    'quota'              => $license['quota'],
                    'activated'          => $license['activated'],
                    'activated_local'    => $license['activated_local'],
                    'secret_key'         => $license['secret_key'],
                    'is_free_localhost'  => $license['is_free_localhost'],
                    'is_block_features'  => $license['is_block_features'],
                    'is_cancelled'       => $license['is_cancelled'],
                    'is_whitelabeled'    => $license['is_whitelabeled'],
                    'environment'        => $license['environment'],
                    'source'             => $license['source'],
                    'expiration'         => $license['expiration'],
                    'freemius_created_at'=> $license['created'],
                    'freemius_updated_at'=> $license['updated'],
                ]
            );
        }
    }
}
