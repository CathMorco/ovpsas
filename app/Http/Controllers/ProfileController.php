<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Office; // Import Office Model
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; // Import Storage for Avatars
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile with filtered publications.
     */
    public function edit(Request $request): View
    {
        $user = Auth::user();

        // 1. Start building the "My Publications" query
        $query = $user->announcements();

        // 2. Apply Filters (Search, Category, Date)
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // 3. Get the results
        $publications = $query->latest()->get();

        return view('user_profile.view', [
            'user' => $user,
            'offices' => Office::all(),   // Pass offices for dropdowns
            'publications' => $publications, // Pass filtered list to view
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Fill basic validated fields (Name, Email)
        $user->fill($request->validated());

        // 2. Manual update for non-standard fields
        // These fields might not be in the default ProfileUpdateRequest
        if ($request->has('phone')) $user->phone = $request->phone;
        if ($request->has('suffix')) $user->suffix = $request->suffix;
        if ($request->has('office_id')) $user->office_id = $request->office_id;
        if ($request->has('designation')) $user->designation = $request->designation;

        // 3. Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            // If user already has an avatar, delete the old file
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Store the new file in 'storage/app/public/avatars'
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // 4. Reset email verification if email changed
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // CLEANUP: Delete the avatar file before deleting the user
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}