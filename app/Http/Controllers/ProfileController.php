<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View|RedirectResponse
    {
        // Redirect admin users to the admin profile page
        if ($request->user()->usertype === 'admin') {
            return redirect('/admin/profile/edit');
        }
        
        // Use regular layout for normal users
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's shipping information.
     */
    public function updateShipping(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'default_recipient_name' => 'nullable|string|max:255',
            'default_recipient_phone' => 'nullable|string|max:20',
            'default_shipping_address' => 'nullable|string',
            'save_shipping_info' => 'boolean'
        ]);

        // Set save_shipping_info to false if not provided
        if (!isset($validated['save_shipping_info'])) {
            $validated['save_shipping_info'] = false;
        }

        $request->user()->update($validated);

        return redirect()->route('profile.edit')->with('status', 'shipping-updated');
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
        $isAdmin = $user->usertype === 'admin';

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Update the user's profile picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfilePicture(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Delete old profile picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Store the new profile picture
        $path = $request->file('profile_picture')->store('profile-pictures', 'public');
        
        // Update user record
        $user->profile_picture = $path;
        $user->save();

        // Redirect based on user type
        if ($user->isAdmin()) {
            return redirect()->route('admin.profile.edit')
                ->with('success', 'Profile picture updated successfully.');
        }
        
        return redirect()->route('profile.edit')
            ->with('status', 'profile-updated');
    }
}
