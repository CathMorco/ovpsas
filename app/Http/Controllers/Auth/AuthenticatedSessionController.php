<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

/**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Check Email & Password
        $request->authenticate();

        $request->session()->regenerate();

        // 2. CHECK ACCOUNT STATUS (Merged Feature)
        // FIXED: Changed 'approved' to 'active' to match the database
        if (Auth::user()->status !== 'active') {
            
            // Log them out immediately
            Auth::guard('web')->logout();

            // Invalidate the session for security
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirect back to login with an error message
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account is pending approval from the Admin.']);
        }

        // 3. If active, proceed to dashboard
        return redirect()->intended(route('dashboard', absolute: false));
    }
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}