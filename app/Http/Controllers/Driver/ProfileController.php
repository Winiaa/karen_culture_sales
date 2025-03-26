<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Store a newly created driver profile.
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'vehicle_type' => 'required|string|max:50',
            'license_number' => 'required|string|max:50',
            'vehicle_plate' => 'required|string|max:20',
            'agreement' => 'required|accepted',
        ]);

        try {
            DB::beginTransaction();
            
            // Create driver profile
            $driver = Driver::create([
                'user_id' => Auth::id(),
                'phone_number' => $request->phone_number,
                'vehicle_type' => $request->vehicle_type,
                'license_number' => $request->license_number,
                'vehicle_plate' => $request->vehicle_plate,
                'is_active' => true, // New drivers start as active
            ]);
            
            // Ensure user has driver usertype
            Auth::user()->update(['usertype' => 'driver']);
            
            DB::commit();
            
            return redirect()->route('driver.dashboard')
                ->with('success', 'Driver profile created successfully. You can now start accepting deliveries.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Failed to create driver profile: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for editing the driver profile.
     */
    public function edit()
    {
        $driver = Auth::user()->driver;
        
        if (!$driver) {
            return redirect()->route('driver.setup');
        }
        
        return view('driver.profile.edit', [
            'driver' => $driver
        ]);
    }
    
    /**
     * Update the driver profile.
     */
    public function update(Request $request)
    {
        $driver = Auth::user()->driver;
        
        if (!$driver) {
            return redirect()->route('driver.setup');
        }
        
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'vehicle_type' => 'required|string|max:50',
            'license_number' => 'required|string|max:50',
            'vehicle_plate' => 'required|string|max:20',
        ]);
        
        $driver->update([
            'phone_number' => $request->phone_number,
            'vehicle_type' => $request->vehicle_type,
            'license_number' => $request->license_number,
            'vehicle_plate' => $request->vehicle_plate,
        ]);
        
        return back()->with('success', 'Driver profile updated successfully.');
    }

    /**
     * Show the form for changing the driver's password.
     */
    public function showPasswordForm()
    {
        $driver = Auth::user()->driver;
        
        if (!$driver) {
            return redirect()->route('driver.setup');
        }
        
        return view('driver.profile.password');
    }

    /**
     * Update the driver's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        Auth::user()->update([
            'password' => bcrypt($request->password),
        ]);

        return back()->with('success', 'Your password has been updated successfully.');
    }
    
    /**
     * Update the driver's profile picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Delete old profile picture if exists
        if ($user->profile_picture) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_picture);
        }

        // Store the new profile picture
        $path = $request->file('profile_picture')->store('profile-pictures', 'public');
        
        // Update user record
        $user->profile_picture = $path;
        $user->save();

        return redirect()->route('driver.profile.edit')
            ->with('success', 'Profile picture updated successfully.');
    }
}
