<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && in_array($request->role, ['customer', 'admin', 'driver'])) {
            $query->where('usertype', $request->role);
        }

        // Sort users
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Load active deliveries count for driver users
        $users = $query->with(['driver' => function($query) {
            $query->withCount(['activeDeliveries']);
        }])->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        // Load relationships based on user type
        if ($user->usertype === 'driver') {
            $user->load(['driver' => function($query) {
                $query->withCount(['activeDeliveries', 'completedDeliveries']);
            }]);
        } elseif ($user->usertype === 'customer') {
            $user->load(['orders' => function($query) {
                $query->with(['orderItems' => function($q) {
                    $q->select('order_id', 'product_id', 'quantity', 'subtotal');
                }])
                ->latest()
                ->take(5);
            }]);
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Check if current admin has super admin privileges
     * In a real app, you would implement proper role-based permission
     * This is a simple implementation for demonstration
     */
    private function isSuperAdmin()
    {
        // All admin users can now manage users
        return Auth::user()->usertype === 'admin';
    }

    /**
     * Check if admin has user management permissions
     * This extends our simple permission system
     */
    private function hasUserManagementPermission()
    {
        // All admin users can now manage users
        return Auth::user()->usertype === 'admin';
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:Users',
            'password' => ['required', Password::defaults()],
            'password_confirmation' => 'required|same:password',
            'usertype' => 'required|in:customer,admin,driver',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'usertype' => $request->usertype,
        ]);

        // Log admin activity
        Log::info('Admin created new user', [
            'admin_id' => Auth::id(),
            'admin_name' => Auth::user()->name,
            'new_user_id' => $user->id,
            'new_user_email' => $user->email,
            'new_user_type' => $user->usertype
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:Users,email,' . $user->id,
            'usertype' => 'required|in:customer,admin,driver',
        ];

        $request->validate($rules);

        $data = $request->only('name', 'email', 'usertype');
        
        // Store original email for notification purposes
        $originalEmail = $user->email;
        $emailChanged = ($originalEmail != $request->email);
        
        // Log the changes being made
        $changes = [];
        foreach ($data as $key => $value) {
            if ($user->$key != $value) {
                $changes[$key] = [
                    'from' => $user->$key,
                    'to' => $value
                ];
            }
        }
        
        $user->update($data);
        
        // Log admin activity
        Log::info('Admin user update', [
            'admin_id' => Auth::id(),
            'admin_name' => Auth::user()->name,
            'target_user_id' => $user->id,
            'changes' => $changes
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Log before deletion
        Log::warning('Admin deleted user', [
            'admin_id' => Auth::id(),
            'admin_name' => Auth::user()->name,
            'deleted_user_id' => $user->id,
            'deleted_user_email' => $user->email,
            'deleted_user_type' => $user->usertype
        ]);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
} 