<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;

class LicenseApiController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'license_key' => 'required',
            'domain' => 'required',
        ]);

        $license = License::all()->firstWhere(
            fn ($l) => $l->license_key === $request->license_key
        );

        if (! $license) {
            return response()->json(['status' => 'invalid'], 404);
        }

        if ($license->status !== 'active') {
            return response()->json(['status' => 'inactive'], 403);
        }

        if ($license->expires_at && $license->expires_at->isPast()) {
            return response()->json(['status' => 'expired'], 403);
        }

        if ($license->domain && $license->domain !== $request->domain) {
            return response()->json(['status' => 'domain_mismatch'], 403);
        }

        return response()->json([
            'status' => 'valid',
            'expires_at' => $license->expires_at,
        ]);
    }
}
