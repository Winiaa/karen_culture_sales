@extends('layouts.driver')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-id-card me-2 text-primary"></i> Driver Profile
        </h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Profile Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-tag me-2"></i> Driver Details
                    </h6>
                    <div class="dropdown no-arrow">
                        <span class="badge {{ $driver->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $driver->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="position-relative mb-3">
                            <img class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;"
                                src="{{ auth()->user()->profile_picture_url }}" alt="Driver Avatar" id="profile-preview">
                            <button type="button" class="btn btn-sm btn-primary position-absolute bottom-0 end-0" 
                                    onclick="document.getElementById('profile-picture-upload').click();">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <form action="{{ route('driver.profile.update-picture') }}" method="POST" enctype="multipart/form-data" id="profile-picture-form">
                            @csrf
                            <div class="mb-3">
                                <input type="file" class="form-control @error('profile_picture') is-invalid @enderror"
                                       id="profile-picture-upload" name="profile_picture" accept="image/*" style="display: none;">
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" 
                                            onclick="document.getElementById('profile-picture-upload').click();">
                                        <i class="fas fa-upload me-1"></i> Choose Image
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-sm" id="save-profile-picture" style="display: none;">
                                        <i class="fas fa-save me-1"></i> Save Image
                                    </button>
                                </div>
                                @error('profile_picture')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                                <div class="form-text small">
                                    Upload a profile picture (JPEG, PNG, or GIF). Max size: 2MB.
                                </div>
                            </div>
                        </form>
                        <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                        <p class="text-muted mb-0">{{ auth()->user()->email }}</p>
                        <p class="mb-0 badge bg-primary">Driver</p>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <i class="fas fa-phone me-2 text-primary"></i> <strong>Phone:</strong> {{ $driver->phone_number }}
                    </div>
                    
                    <div class="mb-3">
                        <i class="fas fa-truck me-2 text-primary"></i> <strong>Vehicle:</strong> {{ ucfirst($driver->vehicle_type) }}
                    </div>
                    
                    <div class="mb-3">
                        <i class="fas fa-id-badge me-2 text-primary"></i> <strong>License:</strong> {{ $driver->license_number }}
                    </div>
                    
                    <div class="mb-3">
                        <i class="fas fa-car me-2 text-primary"></i> <strong>Plate Number:</strong> {{ $driver->vehicle_plate }}
                    </div>
                    
                    <div class="mb-3">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i> <strong>Member Since:</strong> {{ auth()->user()->created_at->format('M d, Y') }}
                    </div>
                    
                    <div class="mb-3">
                        <i class="fas fa-clock me-2 text-primary"></i> <strong>Last Updated:</strong> {{ $driver->updated_at->format('M d, Y') }}
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <a href="{{ route('driver.profile.password') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-key me-1"></i> Change Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profile Form Card -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header -->
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-edit me-2"></i> Update Profile Information
                    </h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <form action="{{ route('driver.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <label for="name" class="col-sm-3 col-form-label">Full Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="name" value="{{ auth()->user()->name }}" disabled>
                                <small class="text-muted">Name cannot be changed. Contact admin for assistance.</small>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="email" class="col-sm-3 col-form-label">Email Address</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" id="email" value="{{ auth()->user()->email }}" disabled>
                                <small class="text-muted">Email cannot be changed. Contact admin for assistance.</small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row mb-3">
                            <label for="phone_number" class="col-sm-3 col-form-label">Phone Number</label>
                            <div class="col-sm-9">
                                <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" 
                                       id="phone_number" name="phone_number" value="{{ old('phone_number', $driver->phone_number) }}" required>
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="vehicle_type" class="col-sm-3 col-form-label">Vehicle Type</label>
                            <div class="col-sm-9">
                                <select class="form-select @error('vehicle_type') is-invalid @enderror" 
                                        id="vehicle_type" name="vehicle_type" required>
                                    <option value="">-- Select Vehicle Type --</option>
                                    <option value="car" {{ old('vehicle_type', $driver->vehicle_type) == 'car' ? 'selected' : '' }}>Car</option>
                                    <option value="motorcycle" {{ old('vehicle_type', $driver->vehicle_type) == 'motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                                    <option value="bicycle" {{ old('vehicle_type', $driver->vehicle_type) == 'bicycle' ? 'selected' : '' }}>Bicycle</option>
                                    <option value="van" {{ old('vehicle_type', $driver->vehicle_type) == 'van' ? 'selected' : '' }}>Van</option>
                                    <option value="truck" {{ old('vehicle_type', $driver->vehicle_type) == 'truck' ? 'selected' : '' }}>Truck</option>
                                </select>
                                @error('vehicle_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="license_number" class="col-sm-3 col-form-label">License Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('license_number') is-invalid @enderror" 
                                       id="license_number" name="license_number" value="{{ old('license_number', $driver->license_number) }}" required>
                                @error('license_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="vehicle_plate" class="col-sm-3 col-form-label">Vehicle Plate Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('vehicle_plate') is-invalid @enderror" 
                                       id="vehicle_plate" name="vehicle_plate" value="{{ old('vehicle_plate', $driver->vehicle_plate) }}" required>
                                @error('vehicle_plate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Driver Status Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Account Status</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            @if($driver->is_active)
                                <span class="badge bg-success p-2">
                                    <i class="fas fa-check-circle me-1"></i> Active
                                </span>
                            @else
                                <span class="badge bg-secondary p-2">
                                    <i class="fas fa-pause-circle me-1"></i> Inactive
                                </span>
                            @endif
                        </div>
                        <div>
                            @if($driver->is_active)
                                <p class="mb-0">You are currently active and can accept deliveries.</p>
                            @else
                                <p class="mb-0">You are currently inactive and cannot accept deliveries. Please contact admin.</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        If you need to change your status, please contact the admin team.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileUpload = document.getElementById('profile-picture-upload');
        const previewImage = document.getElementById('profile-preview');
        const saveButton = document.getElementById('save-profile-picture');
        
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
    });
</script>
@endpush 