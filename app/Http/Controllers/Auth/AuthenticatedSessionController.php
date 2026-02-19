<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Jobs\Freemius\SyncFreemiusLicenses;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use App\Jobs\Freemius\SyncFreemiusCustomerData;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // / Generate the token
        $token = $request->user()->createToken('API TOKEN')->plainTextToken;

        // Check Role
        if ($request->user()->hasRole('Admin')) {
            // Force Admins to Dashboard
            return redirect()->route('dashboard')->with('api_token', $token);
        }

        SyncFreemiusLicenses::dispatch($request->user());
        SyncFreemiusCustomerData::dispatch($request->user());
        // Force regular users to Portal Account with the token
        return redirect()->route('portal.account')->with('api_token', $token);

    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        auth()->user()->tokens()->delete();

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
