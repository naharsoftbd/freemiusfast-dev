<?php

namespace App\Jobs\Freemius;

use App\Services\Freemius\FreemiusCustomerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\CustomerDataSynced;

class SyncFreemiusCustomerData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;      // Retry 5 times

    public $timeout = 30;   // 30 seconds timeout

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
    public function handle(FreemiusCustomerService $freemiusCustomerService): void
    {
        $response = $freemiusCustomerService->syncCustomerData($this->user->id);
        event(new CustomerDataSynced($this->user->id));
    }
}
