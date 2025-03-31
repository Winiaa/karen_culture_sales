@extends('layouts.admin')

@section('title', 'Edit Profile')
@section('subtitle', 'Update your account information')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-user-shield me-2 text-primary"></i> Profile Information
                </h5>
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
                
                <form id="update-profile" method="post" action="{{ route('profile.update') }}" class="mt-3">
                    @csrf
                    @method('patch')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                        @error('name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email" />
                        @error('email')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-karen">Save</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-key me-2 text-primary"></i> Update Password
                </h5>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('password.update') }}" class="mt-3">
                    @csrf
                    @method('put')
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" autocomplete="current-password" />
                        @error('current_password', 'updatePassword')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" autocomplete="new-password" />
                        @error('password', 'updatePassword')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password" />
                        @error('password_confirmation', 'updatePassword')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-karen">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 text-danger">
                <h5 class="mb-0">
                    <i class="fas fa-trash-alt me-2 text-danger"></i> Delete Account
                </h5>
            </div>
            <div class="card-body">
                <p>Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    <i class="fas fa-trash-alt me-1"></i> Delete Account
                </button>
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
                        @error('password', 'userDeletion')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profilePictureInput = document.getElementById('profile-picture-upload');
        const saveButton = document.getElementById('save-profile-picture');
        const imagePreview = document.getElementById('profile-preview');
        const form = document.getElementById('profile-picture-form');
        
        profilePictureInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    saveButton.style.display = 'inline-block';
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
@endpush
@endsection 