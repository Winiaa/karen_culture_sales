@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-page">
                <h1 class="display-1 text-warning">403</h1>
                <h2 class="mb-4">Access Denied</h2>
                <p class="lead mb-4">Sorry, you don't have permission to access this page.</p>
                <div class="mb-4">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="fas fa-home"></i> Return to Home
                    </a>
                </div>
                <div class="mt-4">
                    <p class="text-muted">If you believe this is an error, please contact support.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 