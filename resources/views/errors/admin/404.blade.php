@extends('layouts.admin')

@section('title', '404 - Page Not Found')
@section('subtitle', 'The page you\'re looking for doesn\'t exist.')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="error-page">
                <h1 class="display-1 text-danger">404</h1>
                <p class="lead mb-4">Page Not Found</p>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-tachometer-alt"></i> Return to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 