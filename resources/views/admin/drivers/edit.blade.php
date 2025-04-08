@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Driver</h1>
        <div class="btn-group">
            <a href="{{ route('admin.drivers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <a href="{{ route('admin.drivers.show', $driver) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Driver
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Driver Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.drivers.update', $driver) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user_name">Driver Name (User)</label>
                            <input type="text" class="form-control" id="user_name" value="{{ $driver->user->name }}" disabled>
                            <small class="form-text text-muted">Name can be changed from user profile.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user_email">Email Address</label>
                            <input type="email" class="form-control" id="user_email" value="{{ $driver->user->email }}" disabled>
                            <small class="form-text text-muted">Email can be changed from user profile.</small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number', $driver->phone_number) }}">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="vehicle_type">Vehicle Type</label>
                            <select name="vehicle_type" id="vehicle_type" class="form-control @error('vehicle_type') is-invalid @enderror">
                                <option value="">Select Vehicle Type</option>
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
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="license_number">License Number</label>
                            <input type="text" name="license_number" id="license_number" class="form-control @error('license_number') is-invalid @enderror" value="{{ old('license_number', $driver->license_number) }}">
                            @error('license_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="vehicle_plate">Vehicle Plate Number</label>
                            <input type="text" name="vehicle_plate" id="vehicle_plate" class="form-control @error('vehicle_plate') is-invalid @enderror" value="{{ old('vehicle_plate', $driver->vehicle_plate) }}">
                            @error('vehicle_plate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" value="1" {{ old('is_active', $driver->is_active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Active Driver</label>
                    </div>
                    <small class="form-text text-muted">Inactive drivers cannot be assigned to new deliveries.</small>
                </div>
                
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Driver
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 