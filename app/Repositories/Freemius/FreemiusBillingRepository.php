<?php

namespace App\Repositories\Freemius;

use App\Interfaces\Freemius\FreemiusBillingRepositoryInterface;
use App\Models\Freemius\FreemiusBilling;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FreemiusBillingRepository implements FreemiusBillingRepositoryInterface
{
    public function getUserBilling($fs_user_id)
    {
        $billing = FreemiusBilling::where('fs_user_id', $fs_user_id)->first();   
    }
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

    public function updateByUserId(array $data, $user_id)
    {
        $user = User::find($user_id);
        $data['user_id'] = $user_id;
        $data['email'] = $user->email;

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
