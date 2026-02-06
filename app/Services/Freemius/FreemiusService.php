<?php

namespace App\Services\Freemius;

class FreemiusService
{

    protected  $productId;

    protected $planId;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Verify that the request came from Freemius.
     */
    public function isSignatureValid(array $data, $receivedSignature): bool
    {
        $secretKey = config('freemius.secret_key'); // Set this in your .env
        
        // Remove signature from data to hash the content only
        unset($data['signature']);

        // Freemius typically expects the signature to be an HMAC-SHA256 
        // hash of the JSON-encoded data using your secret key.
        $expectedSignature = hash_hmac('sha256', json_encode($data), $secretKey);

        return hash_equals($expectedSignature, $receivedSignature);
    }
}
