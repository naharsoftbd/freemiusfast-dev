<?php

namespace App\Jobs\Freemius;

use App\Events\CustomerDataSynced;
use App\Services\Freemius\FreemiusCustomerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
