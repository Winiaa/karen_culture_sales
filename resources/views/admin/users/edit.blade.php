@extends('layouts.admin')

@section('title', 'Edit User')
@section('subtitle', 'Update user account information')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit User</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit User: {{ $user->name }}</h5>
            @if($user->id !== auth()->id())
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                    <i class="fas fa-trash me-1"></i> Delete User
                </button>
            </form>
            @endif
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="usertype" class="form-label">Role</label>
                        <select class="form-select @error('usertype') is-invalid @enderror" id="usertype" name="usertype" required>
                            <option value="">Select a role</option>
                            <option value="customer" {{ (old('usertype', $user->usertype) == 'customer') ? 'selected' : '' }}>Customer</option>
                            <option value="admin" {{ (old('usertype', $user->usertype) == 'admin') ? 'selected' : '' }}>Admin</option>
                            <option value="driver" {{ (old('usertype', $user->usertype) == 'driver') ? 'selected' : '' }}>Driver</option>
                        </select>
                        @error('usertype')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i> <strong>Security Notice:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Changing a user's email address will affect their login credentials.</li>
                        <li>For security reasons, admins cannot reset user passwords. Users must reset their own passwords.</li>
                        <li>All account changes are logged and the user will be notified.</li>
                    </ul>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-karen">Update User</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">User Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>User ID:</strong> {{ $user->id }}</p>
                    <p><strong>Joined:</strong> {{ $user->created_at->format('F j, Y') }}</p>
                    <p><strong>Last Updated:</strong> {{ $user->updated_at->format('F j, Y') }}</p>
                </div>
                <div class="col-md-6">
                    @if($user->usertype === 'user')
                        <p><strong>Orders:</strong> {{ $user->orders->count() }}</p>
                    @elseif($user->usertype === 'driver')
                        <p><strong>Driver Status:</strong> 
                            @if($user->driver)
                                {{ $user->driver->is_active ? 'Active' : 'Inactive' }}
                            @else
                                No driver profile yet
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 