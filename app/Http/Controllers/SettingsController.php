<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Office; // REQUIRED for the dropdown
use Illuminate\Support\Facades\Storage; // REQUIRED for avatar handling
use App\Models\User;

class SettingsController extends Controller
{
    /**
     * Display the user's profile settings form.
     */
    public function edit(Request $request): View
    {
        // We pass 'offices' so the dropdown in the view can populate correctly
        return view('profile.edit', [
            'user' => $request->user(),
            'offices' => Office::all(), 
        ]);
    }

    /**
     * Update the user's profile information (Text + Avatar).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // 1. Fill basic text fields (name, email, etc.)
        $user->fill($request->validated());

        // 2. Handle Manual Fields (if they aren't in the validated array)
        if ($request->has('office_id')) $user->office_id = $request->office_id;
        if ($request->has('designation')) $user->designation = $request->designation;
        if ($request->has('phone')) $user->phone = $request->phone;
        if ($request->has('suffix')) $user->suffix = $request->suffix;

        // 3. Handle the Avatar Upload
        if ($request->hasFile('avatar')) {
            // Validate the image specifically
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Delete old avatar from storage if it exists (Clean up)
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar file
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // 4. Handle Email Verification reset if email changed
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // 5. Save changes
        $user->save();

        // Redirect back to the settings page with a success message AND fragment
        // The '#update-password' fragment isn't needed here, but standardizing return is good.
        return Redirect::route('settings.edit')->with('status', 'profile-updated');
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

        // Cleanup: Delete the avatar file from storage before deleting the user
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