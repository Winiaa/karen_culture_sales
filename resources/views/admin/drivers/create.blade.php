@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Driver</h1>
        <a href="{{ route('admin.drivers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Driver Information</h6>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger mb-3">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.drivers.store') }}" method="POST" id="driverForm">
                @csrf
                <input type="hidden" name="user_type" id="user_type_field" value="{{ old('user_type', 'existing') }}">
                
                <div class="form-group mb-4">
                    <div class="form-check form-check-inline">
                        <input type="radio" id="existing_user" name="user_type_selection" value="existing" class="form-check-input" {{ old('user_type', 'existing') == 'existing' ? 'checked' : '' }}>
                        <label class="form-check-label" for="existing_user">Assign Existing Driver</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" id="new_user" name="user_type_selection" value="new" class="form-check-input" {{ old('user_type') == 'new' ? 'checked' : '' }}>
                        <label class="form-check-label" for="new_user">Create New Driver</label>
                    </div>
                </div>
                
                <!-- Existing User Selection -->
                <div id="existing_user_section" style="display: {{ old('user_type', 'existing') == 'existing' ? 'block' : 'none' }}; border-left: 3px solid #4e73df; padding-left: 15px;">
                    <div class="form-group">
                        <label for="user_id"><i class="fas fa-user mr-1"></i> Select Driver</label>
                        <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror">
                            <option value="">Select a Driver</option>
                            @foreach($eligibleUsers as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- New User Section -->
                <div id="new_user_section" style="display: {{ old('user_type') == 'new' ? 'block' : 'none' }}; border-left: 3px solid #4e73df; padding-left: 15px;">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user mr-1"></i> Full Name</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope mr-1"></i> Email Address</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock mr-1"></i> Password</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <hr>
                
                <!-- Driver Information -->
                <div class="section-heading">
                    <i class="fas fa-truck fa-lg"></i>
                    <h5 class="mb-0">Driver Details</h5>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone_number"><i class="fas fa-phone mr-1"></i> Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}" required>
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="vehicle_type"><i class="fas fa-car mr-1"></i> Vehicle Type</label>
                            <select name="vehicle_type" id="vehicle_type" class="form-control @error('vehicle_type') is-invalid @enderror" required>
                                <option value="">Select Vehicle Type</option>
                                <option value="car" {{ old('vehicle_type') == 'car' ? 'selected' : '' }}>Car</option>
                                <option value="motorcycle" {{ old('vehicle_type') == 'motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                                <option value="bicycle" {{ old('vehicle_type') == 'bicycle' ? 'selected' : '' }}>Bicycle</option>
                                <option value="van" {{ old('vehicle_type') == 'van' ? 'selected' : '' }}>Van</option>
                                <option value="truck" {{ old('vehicle_type') == 'truck' ? 'selected' : '' }}>Truck</option>
                            </select>
                            @error('vehicle_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="license_number"><i class="fas fa-id-card mr-1"></i> License Number</label>
                            <input type="text" name="license_number" id="license_number" class="form-control @error('license_number') is-invalid @enderror" value="{{ old('license_number') }}">
                            @error('license_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="vehicle_plate"><i class="fas fa-clipboard-list mr-1"></i> Vehicle Plate Number</label>
                            <input type="text" name="vehicle_plate" id="vehicle_plate" class="form-control @error('vehicle_plate') is-invalid @enderror" value="{{ old('vehicle_plate') }}">
                            @error('vehicle_plate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" checked>
                        <label class="form-check-label" for="is_active">
                            <i class="fas fa-toggle-on text-success mr-1"></i> Active Driver
                        </label>
                        <small class="form-text text-muted">Active drivers can be assigned to deliveries. Uncheck this if the driver is not currently available.</small>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                        <i class="fas fa-save mr-1"></i> Create Driver
                    </button>
                    <div class="spinner-overlay d-none" id="formSpinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="mt-2">Creating driver...</div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Driver form initialized');
        
        const existingUserRadio = document.getElementById('existing_user');
        const newUserRadio = document.getElementById('new_user');
        const existingUserSection = document.getElementById('existing_user_section');
        const newUserSection = document.getElementById('new_user_section');
        const userTypeField = document.getElementById('user_type_field');
        const driverForm = document.getElementById('driverForm');
        const submitBtn = document.getElementById('submitBtn');
        const formSpinner = document.getElementById('formSpinner');
        
        function toggleUserSections() {
            const isExisting = existingUserRadio.checked;
            existingUserSection.style.display = isExisting ? 'block' : 'none';
            newUserSection.style.display = isExisting ? 'none' : 'block';
            userTypeField.value = isExisting ? 'existing' : 'new';
        }
        
        existingUserRadio.addEventListener('change', toggleUserSections);
        newUserRadio.addEventListener('change', toggleUserSections);
        
        // Initial toggle based on the selected radio button
        toggleUserSections();
        
        // Form validation and submission
        driverForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submission started');
            
            // Validate the form
            if (!validateForm()) {
                console.log('Form validation failed');
                return false;
            }
            
            console.log('Form validation passed');
            
            // Show spinner and disable button
            submitBtn.classList.add('d-none');
            formSpinner.classList.remove('d-none');
            
            // CRITICAL: Ensure the user_type field has the correct value before submission
            userTypeField.value = existingUserRadio.checked ? 'existing' : 'new';
            console.log('user_type field value:', userTypeField.value);
            
            // For debugging in console
            if (userTypeField.value === 'existing') {
                console.log('Selected user ID:', document.getElementById('user_id').value);
            } else {
                console.log('Creating new user with name:', document.getElementById('name').value);
            }
            
            // Submit the form
            console.log('Submitting form with user_type:', userTypeField.value);
            this.submit();
        });
        
        function validateForm() {
            console.log('Validating form...');
            let isValid = true;
            let errorMessage = '';
            
            const userType = existingUserRadio.checked ? 'existing' : 'new';
            console.log('User type for validation:', userType);
            
            // Clear previous validation styling
            const formElements = driverForm.querySelectorAll('.form-control');
            formElements.forEach(el => el.classList.remove('is-invalid'));
            
            // Validate based on user type
            if (userType === 'existing') {
                const userId = document.getElementById('user_id').value;
                console.log('Validating existing user, user_id:', userId);
                
                if (!userId) {
                    isValid = false;
                    errorMessage = 'Please select a user';
                    document.getElementById('user_id').classList.add('is-invalid');
                    console.log('User ID validation failed');
                }
            } else {
                // Validate new user fields
                const name = document.getElementById('name').value.trim();
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value;
                
                console.log('Validating new user fields: name present:', !!name, 'email present:', !!email);
                
                if (!name) {
                    isValid = false;
                    errorMessage = 'Please enter a name';
                    document.getElementById('name').classList.add('is-invalid');
                }
                
                if (!email) {
                    isValid = false;
                    errorMessage = 'Please enter an email';
                    document.getElementById('email').classList.add('is-invalid');
                } else if (!isValidEmail(email)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid email address';
                    document.getElementById('email').classList.add('is-invalid');
                }
                
                if (!password) {
                    isValid = false;
                    errorMessage = 'Please enter a password';
                    document.getElementById('password').classList.add('is-invalid');
                } else if (password.length < 8) {
                    isValid = false;
                    errorMessage = 'Password must be at least 8 characters';
                    document.getElementById('password').classList.add('is-invalid');
                }
            }
            
            // Validate common fields
            const phoneNumber = document.getElementById('phone_number').value.trim();
            const vehicleType = document.getElementById('vehicle_type').value;
            
            console.log('Common fields: phone:', phoneNumber, 'vehicle type:', vehicleType);
            
            if (!phoneNumber) {
                isValid = false;
                errorMessage = 'Please enter a phone number';
                document.getElementById('phone_number').classList.add('is-invalid');
            }
            
            if (!vehicleType) {
                isValid = false;
                errorMessage = 'Please select a vehicle type';
                document.getElementById('vehicle_type').classList.add('is-invalid');
            }
            
            if (!isValid) {
                alert(errorMessage);
                console.log('Validation error:', errorMessage);
            }
            
            return isValid;
        }
        
        function isValidEmail(email) {
            const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }
    });
</script>
@endpush

@push('styles')
<style>
    #existing_user_section, #new_user_section {
        transition: all 0.3s ease;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .section-heading {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .section-heading i {
        margin-right: 8px;
        color: var(--primary-color);
    }
    
    .spinner-overlay {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    .is-invalid {
        border-color: #dc3545 !important;
    }
    
    .text-danger {
        color: #dc3545 !important;
    }
    
    .form-check-input {
        margin-top: 0.25rem;
        cursor: pointer;
    }
    
    .form-check-label {
        cursor: pointer;
    }
    
    /* Improved form group spacing */
    .form-group {
        margin-bottom: 1.25rem;
    }
    
    /* Better focus states */
    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    /* Make the alert more prominent */
    .alert {
        border-left: 4px solid;
    }
    
    .alert-danger {
        border-left-color: #dc3545;
    }
    
    /* Fix for select dropdowns */
    select.form-control {
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
    }
</style>
@endpush 