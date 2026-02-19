<?php

namespace App\Jobs;

use App\Models\Freemius\FreemiusBilling;
use App\Models\User;
use App\Traits\FreemiusConfigTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SyncUserToFreemius implements ShouldQueue
{
    use Dispatchable, FreemiusConfigTrait, InteractsWithQueue, Queueable, SerializesModels;

    // The number of times the job may be attempted if Freemius is down
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user)
    {
        $this->initFreemius(); // initialize shared Freemius config
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = Http::withHeaders($this->headers)
            ->post("{$this->baseUrl}/users.json", [
                'email' => $this->user->email,
                'name' => $this->user->name,
                'first' => $this->user->first_name,
                'last' => $this->user->last_name,
                'is_verified' => true,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            $freemiususer = FreemiusBilling::create([
                'fs_user_id' => $data['id'],
                'user_id' => $user->id,
                'first' => $user->first_name,
                'last' => $user->last_name,
                'email' => $user->email,
            ]);
        } else {
            // This will trigger a retry based on your $tries property
            throw new \Exception('Freemius API failed: '.$response->body());
        }
    }
}
