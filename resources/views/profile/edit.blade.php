@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">My Profile</h1>
    
    @if(session('status') === 'profile-updated')
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Your profile has been updated successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    @if(session('status') === 'password-updated')
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Your password has been updated successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    @if(session('status') === 'shipping-updated')
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Your shipping information has been updated successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Profile Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h3 class="card-title m-0">
                        <i class="fas fa-user-circle me-2 text-primary"></i> Profile Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <div class="position-relative mb-3">
                                <img src="{{ $user->profile_picture_url }}" alt="{{ $user->name }}" 
                                     class="img-thumbnail rounded-circle" width="150" height="150" id="profile-preview">
                                <button type="button" class="btn btn-sm btn-primary position-absolute bottom-0 end-0" 
                                        onclick="document.getElementById('profile-picture-upload').click();">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <form action="{{ route('profile.update-picture') }}" method="POST" enctype="multipart/form-data" id="profile-picture-form">
                                @csrf
                                <div class="mb-3">
                                    <label for="profile-picture-upload" class="form-label">Profile Picture</label>
                                    <input type="file" class="form-control @error('profile_picture') is-invalid @enderror"
                                           id="profile-picture-upload" name="profile_picture" accept="image/*" style="display: none;">
                                    <div class="d-flex mt-2">
                                        <button type="button" class="btn btn-outline-primary" 
                                                onclick="document.getElementById('profile-picture-upload').click();">
                                            <i class="fas fa-upload me-1"></i> Choose Image
                                        </button>
                                        <button type="submit" class="btn btn-karen ms-2" id="save-profile-picture" style="display: none;">
                                            <i class="fas fa-save me-1"></i> Save Image
                                        </button>
                                    </div>
                                    @error('profile_picture')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Upload a profile picture (JPEG, PNG, or GIF). Max file size: 2MB.
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-karen">Save Changes</button>
                    </form>
                </div>
            </div>
            
            <!-- Shipping Information -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h3 class="card-title m-0">
                        <i class="fas fa-shipping-fast me-2 text-primary"></i> Default Shipping Information
                    </h3>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('profile.update.shipping') }}">
                        @csrf
                        @method('patch')
                        
                        <div class="mb-3">
                            <label for="default_recipient_name" class="form-label">Recipient Name</label>
                            <input type="text" class="form-control @error('default_recipient_name') is-invalid @enderror" 
                                   id="default_recipient_name" name="default_recipient_name" value="{{ old('default_recipient_name', $user->default_recipient_name) }}">
                            @error('default_recipient_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="default_recipient_phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control @error('default_recipient_phone') is-invalid @enderror" 
                                   id="default_recipient_phone" name="default_recipient_phone" value="{{ old('default_recipient_phone', $user->default_recipient_phone) }}">
                            @error('default_recipient_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="default_shipping_address" class="form-label">Delivery Address</label>
                            <textarea class="form-control @error('default_shipping_address') is-invalid @enderror" 
                                      id="default_shipping_address" name="default_shipping_address" rows="3">{{ old('default_shipping_address', $user->default_shipping_address) }}</textarea>
                            @error('default_shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input @error('save_shipping_info') is-invalid @enderror" 
                                   id="save_shipping_info" name="save_shipping_info" value="1" {{ $user->save_shipping_info ? 'checked' : '' }}>
                            <label class="form-check-label" for="save_shipping_info">Save as default for future orders</label>
                            @error('save_shipping_info')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-karen">Save Shipping Information</button>
                    </form>
                </div>
            </div>
            
            <!-- Update Password -->
            <div class="card">
                <div class="card-header bg-white">
                    <h3 class="card-title m-0">
                        <i class="fas fa-lock me-2 text-primary"></i> Update Password
                    </h3>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" name="password_confirmation" required>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-karen">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar with Account Info -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h3 class="card-title m-0">
                        <i class="fas fa-cog me-2 text-primary"></i> Account Settings
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-25 text-primary p-3 me-3">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-0">{{ $user->email }}</p>
                        </div>
                    </div>
                    
                    <div class="list-group list-group-flush">
                        <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-shopping-bag me-2"></i> My Orders
                            </div>
                            @php
                                try {
                                    $orderCount = Auth::user()->orders()->count();
                                } catch (\Exception $e) {
                                    $orderCount = 0;
                                }
                            @endphp
                            <span class="badge bg-primary rounded-pill">{{ $orderCount }}</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Delete Account -->
            <div class="card">
                <div class="card-header bg-white">
                    <h3 class="card-title m-0">
                        <i class="fas fa-trash-alt me-2 text-danger"></i> Delete Account
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                    
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAccountModalLabel">Delete Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                
                <div class="modal-body">
                    <p class="text-danger">Are you sure you want to delete your account? This action cannot be undone.</p>
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Enter your password to confirm:</label>
                        <input type="password" class="form-control" id="delete_password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileUpload = document.getElementById('profile-picture-upload');
        const previewImage = document.getElementById('profile-preview');
        const saveButton = document.getElementById('save-profile-picture');
        const form = document.getElementById('profile-picture-form');
        
        profileUpload.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    saveButton.style.display = 'block';
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Handle form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    throw new Error('Network response was not ok');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while uploading the profile picture. Please try again.');
            });
        });
    });
</script>
@endsection
