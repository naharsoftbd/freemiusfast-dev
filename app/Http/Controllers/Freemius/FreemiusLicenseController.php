<?php

namespace App\Http\Controllers\Freemius;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Freemius\FreemiusLicenseService;
use Inertia\Inertia;

class FreemiusLicenseController extends Controller
{

    protected $freemiusLicenseService;

    protected $user;

    public function __construct(FreemiusLicenseService $freemiusLicenseService)
    {
        $this->user = auth()->user();
        $this->freemiusLicenseService = $freemiusLicenseService;
    }

    public function index()
    {
        
        
        $licenses = $this->freemiusLicenseService->getLicenseByUser($this->user->id);

        return Inertia::render('Freemius/Licenses/Index', [
                'licenses' => $licenses->map(function ($license) {
                    return [
                        'id' => $license->id,
                        'plan' => $license->plan_id,
                        'issued_at' => $license->freemius_created_at,
                        'key' => $license->secret_key,
                        'user' => $license->fs_user_id,
                        'subscription' => $license->pricing_id,
                        'quota' => $license->quota,
                        'expiration' => $license->expiration,
                    ];
                }),
            ]);

    }
}
