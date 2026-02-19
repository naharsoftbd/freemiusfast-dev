<?php

namespace App\Jobs;

use App\Models\Freemius\FreemiusProduct;
use App\Services\Freemius\FreemiusProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncFreemiusProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;      // Retry 5 times

    public $timeout = 30;   // 30 seconds timeout

    /**
     * Create a new job instance.
     */
    public function __construct(public FreemiusProduct $product, public bool $isUpdate = false)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(FreemiusProductService $freemiusProductService): void
    {
        if ($this->product->is_synced) {
            return;
        }

        if ($this->isUpdate) {
            $updateresponse = $freemiusProductService->sysncUpdateProduct($this->product);
        } else {
            $response = $freemiusProductService->syncProduct($this->product->freemius_product_id);
        }

    }
}
