<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Office; // MERGED: Required for the dropdown
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // MERGED: Fetch offices so the registration form dropdown works
        $offices = Office::all();

        return view('auth.register', compact('offices'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
            // MERGED: Use the safer validation rule
            'office_id' => ['required', 'exists:offices,id'], 
            'designation' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'office_id' => $request->office_id,
            'designation' => $request->designation,
            
            // MERGED: Force status to pending so they can't access the site yet
            'status' => 'pending', 
        ]);

        event(new Registered($user));

        // ---------------------------------------------------------
        // MERGED: STOP AUTO LOGIN
        // We comment this out so they don't get into the dashboard immediately
        // Auth::login($user); 
        // ---------------------------------------------------------

        // Redirect to login page with a success message
        return redirect('/login')->with('status', 'Registration successful! Please wait for Admin approval.');
    }
}