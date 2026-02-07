<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Freemius\FreemiusBilling;
use Illuminate\Http\Request;

class FreemiusBillingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(FreemiusBilling::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'fs_user_id' => 'required|integer|unique:freemius_billings,fs_user_id',
            'business_name' => 'nullable|string',
            'first' => 'nullable|string',
            'last' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'website' => 'nullable|url',
            'tax_id' => 'nullable|string',
            'address_street' => 'nullable|string',
            'address_apt' => 'nullable|string',
            'address_city' => 'nullable|string',
            'address_state' => 'nullable|string',
            'address_zip' => 'nullable|string',
            'address_country' => 'nullable|string',
            'address_country_code' => 'nullable|string|max:10',
            'fs_created_at' => 'nullable|date',
            'fs_updated_at' => 'nullable|date',
        ]);

        $billing = FreemiusBilling::create($data);

        return response()->json($billing, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $billing = FreemiusBilling::findOrFail($id);
        return response()->json($billing);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $billing = FreemiusBilling::findOrFail($id);

        $data = $request->validate([
            'fs_user_id' => ['nullable','integer', Rule::unique('freemius_billings')->ignore($billing->id)],
            'business_name' => 'nullable|string',
            'first' => 'nullable|string',
            'last' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'website' => 'nullable|url',
            'tax_id' => 'nullable|string',
            'address_street' => 'nullable|string',
            'address_apt' => 'nullable|string',
            'address_city' => 'nullable|string',
            'address_state' => 'nullable|string',
            'address_zip' => 'nullable|string',
            'address_country' => 'nullable|string',
            'address_country_code' => 'nullable|string|max:10',
            'fs_created_at' => 'nullable|date',
            'fs_updated_at' => 'nullable|date',
        ]);

        $billing->update($data);

        return response()->json($billing);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $billing = FreemiusBilling::findOrFail($id);
        $billing->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    // In FreemiusBillingController.php

    public function showByFsUserId($fs_user_id)
    {
        $billing = FreemiusBilling::where('fs_user_id', $fs_user_id)->firstOrFail();
        return response()->json($billing);
    }

    public function updateByFsUserId(Request $request, $fs_user_id)
    {
        $billing = FreemiusBilling::where('fs_user_id', $fs_user_id)->firstOrFail();

        $data = $request->validate([
            'business_name' => 'nullable|string',
            'phone' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'address_street' => 'nullable|string',
            'address_apt' => 'nullable|string',
            'address_city' => 'nullable|string',
            'address_state' => 'nullable|string',
            'address_zip' => 'nullable|string',
            'address_country_code' => 'nullable|string|max:10',
        ]);

        $billing->update($data);

        return response()->json($billing);
    }

}
