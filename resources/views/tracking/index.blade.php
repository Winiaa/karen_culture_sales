@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="text-center mb-4">
                <h2 class="fw-bold">Track Your Order</h2>
                <p class="text-muted">Enter your tracking number to see real-time delivery status</p>
            </div>
            
            <div class="card shadow">
                <div class="card-body p-4">
                    
                    @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif
                    
                    <form action="{{ route('tracking.track') }}" method="POST" class="tracking-form">
                        @csrf
                        <div class="mb-4">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input 
                                    type="text" 
                                    class="form-control form-control-lg border-start-0 @error('tracking_number') is-invalid @enderror" 
                                    id="tracking_number" 
                                    name="tracking_number" 
                                    placeholder="Enter your tracking number" 
                                    value="{{ old('tracking_number') }}"
                                    required
                                >
                                @error('tracking_number')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-text text-center mt-2">
                                Example: TRK-ABC123DEF
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-shipping-fast me-2"></i>Track Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mb-3">
                                <i class="fas fa-truck"></i>
                            </div>
                            <h5>Where is my order?</h5>
                            <p class="text-muted">Track the current status of your package with real-time updates.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mb-3">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h5>Delivery Schedule</h5>
                            <p class="text-muted">See estimated delivery dates and plan accordingly.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mb-3">
                                <i class="fas fa-history"></i>
                            </div>
                            <h5>Delivery History</h5>
                            <p class="text-muted">View the complete journey of your package from our warehouse to your door.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.tracking-form {
    max-width: 600px;
    margin: 0 auto;
}
.feature-icon {
    width: 50px;
    height: 50px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}
</style>
@endsection 