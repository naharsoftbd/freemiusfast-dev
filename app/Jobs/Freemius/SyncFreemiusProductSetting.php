<?php

namespace App\Jobs\Freemius;

use App\Models\Freemius\FreemiusSetting;
use App\Services\Freemius\FreemiusSettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\ProductSynced;

class SyncFreemiusProductSetting implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;      // Retry 5 times

    public $timeout = 30;   // 30 seconds timeout

    /**
     * Create a new job instance.
     */
    public function __construct(public FreemiusSetting $setting)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(FreemiusSettingService $freemiusSettingService): void
    {
        // Refresh model to avoid stale data
        $setting = $this->setting->fresh();

        if (! $setting) {
            return;
        }

        $response = $freemiusSettingService->syncProductSetting($setting);
        event(new ProductSynced($setting->id));
    }
}
