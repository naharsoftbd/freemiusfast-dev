<?php

namespace App\Http\Controllers\Api\Freemius;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FreemiusUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function me(Request $request)
    {        
        $user = auth()->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $billinginfo = $user?->freemiusBilling;


        return response()->json([
            'success' => true,
            'data' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'first_name' => $billinginfo?->first,
                'last_name' => $billinginfo?->last,
                'phone' => $billinginfo?->phone,
                'created_at' => $request->user()->created_at,
            ],
            'message' => 'User info retrieved successfully',
        ]);
    }
}
