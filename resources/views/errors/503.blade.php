@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-page">
                <h1 class="display-1 text-warning">503</h1>
                <h2 class="mb-4">Be Right Back!</h2>
                <p class="lead mb-4">We're performing some maintenance to improve your experience.</p>
                <div class="mb-4">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="fas fa-home"></i> Return to Home
                    </a>
                </div>
                <div class="mt-4">
                    <p class="text-muted">Please check back soon. We'll be back online shortly.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 