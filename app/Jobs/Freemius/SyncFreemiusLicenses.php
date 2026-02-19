<?php

namespace App\Jobs\Freemius;

use App\Services\Freemius\FreemiusLicenseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class SyncFreemiusLicenses implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(FreemiusLicenseService $freemiusLicenseService): void
    {
        $response = $freemiusLicenseService->syncUserLicenses($this->user->id);
    }
}
