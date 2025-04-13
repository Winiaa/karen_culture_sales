@extends('layouts.admin')

@section('title', '503 - Service Unavailable')
@section('subtitle', 'We\'ll be back shortly.')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="error-page">
                <h1 class="display-1 text-warning">503</h1>
                <p class="lead mb-4">Service Unavailable</p>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-tachometer-alt"></i> Return to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 