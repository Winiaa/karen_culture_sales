<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\User;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    /**
     * Display a listing of all drivers.
     */
    public function index(): View
    {
        $drivers = Driver::with('user')
            ->withCount(['activeDeliveries', 'completedDeliveries'])
            ->latest()
            ->paginate(10);
            
        return view('admin.drivers.index', [
            'drivers' => $drivers
        ]);
    }
    
    /**
     * Show the form for creating a new driver.
     */
    public function create(): View
    {
        // Get users who are already marked as drivers but don't have a driver profile yet
        $eligibleUsers = User::whereDoesntHave('driver')
            ->where('usertype', 'driver')
            ->get();
            
        return view('admin.drivers.create', [
            'eligibleUsers' => $eligibleUsers
        ]);
    }
    
    /**
     * Store a newly created driver in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Log the incoming request data for debugging
        Log::info('Driver creation request data:', $request->all());
        
        // First determine if we're using an existing user or creating a new one
        $isNewUser = $request->input('user_type') === 'new';
        
        Log::info('User type selected: ' . ($isNewUser ? 'new' : 'existing'));
        
        // Set up validation rules based on the user type
        $rules = [
            'phone_number' => 'required|string|max:20',
            'vehicle_type' => 'required|string|max:50',
            'license_number' => 'nullable|string|max:50',
            'vehicle_plate' => 'nullable|string|max:20',
            'is_active' => 'sometimes|boolean',
        ];
        
        // Add specific validation rules based on user type
        if ($isNewUser) {
            $rules['name'] = 'required|string|max:255';
            $rules['email'] = 'required|email|unique:Users,email';
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['user_id'] = 'required|exists:Users,id';
        }
        
        // Validate the request data
        $validated = $request->validate($rules);
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            if ($isNewUser) {
                // Create a new user
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'usertype' => 'driver',
                ]);
                
                $userId = $user->id;
                Log::info('Created new user with ID: ' . $userId);
            } else {
                // Use existing user
                $userId = $request->user_id;
                $user = User::findOrFail($userId);
                
                // Only update usertype if it's not already set to driver
                if ($user->usertype !== 'driver') {
                    $user->update(['usertype' => 'driver']);
                    Log::info('Updated existing user ' . $userId . ' to driver type');
                }
            }
            
            // Create the driver profile
            $driver = Driver::create([
                'user_id' => $userId,
                'phone_number' => $request->phone_number,
                'vehicle_type' => $request->vehicle_type,
                'license_number' => $request->license_number ?? '',
                'vehicle_plate' => $request->vehicle_plate ?? '',
                'is_active' => $request->has('is_active'),
            ]);
            
            Log::info('Driver created successfully with ID: ' . $driver->id);
            
            DB::commit();
            
            return redirect()->route('admin.drivers.index')
                ->with('success', 'Driver created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating driver: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return back()->withInput()
                ->with('error', 'Error creating driver: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the specified driver.
     */
    public function show(Driver $driver): View
    {
        $driver->load('user');
        
        // Get recent deliveries
        $recentDeliveries = $driver->deliveries()
            ->with('order')
            ->latest()
            ->take(10)
            ->get();
            
        // Get delivery statistics
        $deliveryStats = [
            'total' => $driver->completedDeliveries()->count(),
            'active' => $driver->activeDeliveries()->count(),
            'completed' => $driver->completedDeliveries()->count(),
            'failed' => $driver->deliveries()->where('delivery_status', 'failed')->count(),
        ];
        
        return view('admin.drivers.show', [
            'driver' => $driver,
            'recentDeliveries' => $recentDeliveries,
            'deliveryStats' => $deliveryStats,
        ]);
    }
    
    /**
     * Show the form for editing the specified driver.
     */
    public function edit(Driver $driver): View
    {
        $driver->load('user');
        
        return view('admin.drivers.edit', [
            'driver' => $driver
        ]);
    }
    
    /**
     * Update the specified driver in storage.
     */
    public function update(Request $request, Driver $driver): RedirectResponse
    {
        $request->validate([
            'phone_number' => 'required|string|max:255',
            'vehicle_type' => 'required|in:car,motorcycle,bicycle,van,truck',
            'license_number' => 'required|string|max:255',
            'vehicle_plate' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        // Get all the input data including is_active
        $data = $request->only([
            'phone_number', 
            'vehicle_type', 
            'license_number', 
            'vehicle_plate'
        ]);
        
        // Handle is_active separately to ensure proper boolean conversion
        $data['is_active'] = $request->has('is_active');

        // Log the changes being made
        $changes = [];
        foreach ($data as $key => $value) {
            if ($driver->$key != $value) {
                $changes[$key] = [
                    'from' => $driver->$key,
                    'to' => $value
                ];
            }
        }

        // Begin transaction to ensure data consistency
        DB::beginTransaction();
        
        try {
            // Update the driver
            $driver->update($data);
            
            // Ensure the user type remains as 'driver'
            $driver->user()->update(['usertype' => 'driver']);
            
            DB::commit();
            
            // Log admin activity
            Log::info('Admin driver update', [
                'admin_id' => Auth::id(),
                'admin_name' => Auth::user()->name,
                'driver_id' => $driver->id,
                'changes' => $changes
            ]);

            return redirect()->route('admin.drivers.index')
                ->with('success', 'Driver updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating driver: ' . $e->getMessage());
            
            return back()->with('error', 'There was a problem updating the driver: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified driver from storage.
     */
    public function destroy(Driver $driver): RedirectResponse
    {
        // Check if driver has active deliveries
        if ($driver->activeDeliveries()->count() > 0) {
            return back()->with('error', 'Cannot delete driver with active deliveries.');
        }
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Reassign past deliveries to null
            Delivery::where('driver_id', $driver->id)
                ->update(['driver_id' => null]);
                
            // Update user type back to normal user
            $driver->user()->update(['usertype' => 'customer']);
            
            // Delete the driver
            $driver->delete();
            
            DB::commit();
            
            return redirect()->route('admin.drivers.index')
                ->with('success', 'Driver deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'There was a problem deleting the driver: ' . $e->getMessage());
        }
    }
    
    /**
     * Toggle the active status of a driver.
     */
    public function toggleActive(Driver $driver): RedirectResponse
    {
        $driver->update([
            'is_active' => !$driver->is_active
        ]);
        
        $status = $driver->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.drivers.show', $driver)
            ->with('success', "Driver {$status} successfully.");
    }
}
