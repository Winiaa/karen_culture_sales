@extends('layouts.driver-setup')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Driver Account Setup</h4>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-truck-moving fa-4x text-primary mb-3"></i>
                        <h5>Welcome to the Karen Culture Delivery Team!</h5>
                        <p class="text-muted">Please complete your driver profile to continue.</p>
                    </div>

                    <form action="{{ route('driver.profile.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="vehicle_type" class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('vehicle_type') is-invalid @enderror" 
                                id="vehicle_type" name="vehicle_type" required>
                                <option value="" selected disabled>Select vehicle type</option>
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

                        <div class="mb-3">
                            <label for="license_number" class="form-label">License Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('license_number') is-invalid @enderror" 
                                id="license_number" name="license_number" value="{{ old('license_number') }}" required>
                            @error('license_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="vehicle_plate" class="form-label">Vehicle Plate Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('vehicle_plate') is-invalid @enderror" 
                                id="vehicle_plate" name="vehicle_plate" value="{{ old('vehicle_plate') }}" required>
                            @error('vehicle_plate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="agreement" id="agreement" required>
                            <label class="form-check-label" for="agreement">
                                I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a> for Karen Culture Delivery Drivers
                            </label>
                            @error('agreement')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Complete Setup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Driver Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Karen Culture Delivery Driver Agreement</h6>
                <p>This agreement outlines the terms and conditions for delivery drivers working with Karen Culture online store.</p>
                
                <h6>1. Responsibilities</h6>
                <ul>
                    <li>Collect packages from our warehouse or store locations</li>
                    <li>Deliver packages to customers in a timely and professional manner</li>
                    <li>Handle all items with care and ensure they remain in good condition</li>
                    <li>Verify customer identity before handing over packages</li>
                    <li>Collect payment for cash-on-delivery orders when applicable</li>
                    <li>Report any issues or delays immediately</li>
                    <li>Maintain your vehicle in good working condition</li>
                </ul>
                
                <h6>2. Payment Terms</h6>
                <p>Payment is based on successful deliveries completed. Rates vary based on distance, package size, and delivery time requirements.</p>
                
                <h6>3. Code of Conduct</h6>
                <ul>
                    <li>Always be professional and courteous to customers</li>
                    <li>Wear appropriate clothing and identification</li>
                    <li>Do not open or tamper with packages</li>
                    <li>Respect customer privacy and data confidentiality</li>
                    <li>Do not use or be under the influence of alcohol or drugs while on duty</li>
                </ul>
                
                <h6>4. Termination</h6>
                <p>Karen Culture reserves the right to terminate this agreement at any time due to:</p>
                <ul>
                    <li>Repeated late deliveries or missed pickups</li>
                    <li>Customer complaints about service quality</li>
                    <li>Violation of the code of conduct</li>
                    <li>Mishandling of packages or payments</li>
                </ul>
                
                <h6>5. Insurance and Liability</h6>
                <p>Drivers are responsible for their own insurance coverage. Karen Culture is not liable for any accidents, injuries, or damages that occur during the delivery process.</p>
                
                <p class="mb-0"><strong>By agreeing to these terms, you acknowledge that you have read, understood, and agree to abide by all the provisions outlined in this agreement.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection 