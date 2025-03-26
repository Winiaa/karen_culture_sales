@extends('layouts.driver')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-lock me-2 text-primary"></i> Change Password
        </h1>
        <a href="{{ route('driver.profile.edit') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-user fa-sm text-white-50 me-1"></i> Back to Profile
        </a>
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
        <!-- Password Form Card -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header -->
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-key me-2"></i> Update Password
                    </h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <form action="{{ route('driver.profile.update-password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <label for="current_password" class="col-sm-3 col-form-label">
                                <i class="fas fa-shield-alt me-1 text-muted"></i> Current Password
                            </label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password" required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="password" class="col-sm-3 col-form-label">
                                <i class="fas fa-unlock-alt me-1 text-muted"></i> New Password
                            </label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Password must be at least 8 characters long.</small>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="password_confirmation" class="col-sm-3 col-form-label">
                                <i class="fas fa-check-circle me-1 text-muted"></i> Confirm Password
                            </label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key me-1"></i> Update Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Password Tips Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header -->
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i> Password Tips
                    </h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="text-info font-weight-bold">
                            <i class="fas fa-shield-alt me-2"></i> Strong Password Guidelines
                        </h5>
                        <p class="mb-0 text-muted">Follow these guidelines to create a secure password:</p>
                    </div>
                    
                    <ul class="list-group list-group-flush mb-0">
                        <li class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="me-3 text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>At least 8 characters long</div>
                        </li>
                        <li class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="me-3 text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>Mix of uppercase and lowercase letters</div>
                        </li>
                        <li class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="me-3 text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>Include numbers and special characters</div>
                        </li>
                        <li class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="me-3 text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>Avoid using personal information</div>
                        </li>
                        <li class="list-group-item d-flex align-items-center border-0 px-0">
                            <div class="me-3 text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>Don't reuse passwords from other sites</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 