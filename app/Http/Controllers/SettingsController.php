<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\Office;

class SettingsController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'offices' => Office::all(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Capture validated data (excluding avatar for manual handling)
        $data = $request->validated();

        // 2. Handle the Avatar File Upload
        if ($request->hasFile('avatar')) {
            // Delete the old avatar from storage if it exists to save disk space
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store the new file in 'storage/app/public/avatars'
            // This returns the relative path (e.g., 'avatars/random_name.jpg')
            $path = $request->file('avatar')->store('avatars', 'public');
            
            // Add the path to our data array
            $data['avatar'] = $path;
        }

        // 3. Reset email verification if email changed
        if ($user->email !== $data['email']) {
            $user->email_verified_at = null;
        }

        // 4. Mass-update the user with the validated data
        $user->update($data);

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

        // Clean up: Remove avatar from storage before deleting the account
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