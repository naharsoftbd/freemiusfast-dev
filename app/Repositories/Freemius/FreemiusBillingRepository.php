<?php

namespace App\Repositories\Freemius;

use App\Models\Freemius\FreemiusBilling;
use App\Interfaces\Freemius\FreemiusBillingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class FreemiusBillingRepository implements FreemiusBillingRepositoryInterface
{
    public function updateByFsUserId(array $data, $fs_user_id)
    {
        $user = Auth::user();
        $billing = FreemiusBilling::where('fs_user_id', $fs_user_id)->first();


        $data['user_id'] = auth()->id();
        $data['email'] = $user->email;
        $data['fs_user_id'] = $fs_user_id;

       $lookup = ['fs_user_id' => $data['fs_user_id']];

        try {
            $billing = FreemiusBilling::updateOrCreate($lookup, $data);
            return response()->json($billing);
        } catch (\Exception $e) {
            Log::error('Billing Update Failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Duplicate entry detected'], 422);
        }

    }
}
