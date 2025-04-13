@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-page">
                <h1 class="display-1 text-warning">419</h1>
                <h2 class="mb-4">Page Expired</h2>
                <p class="lead mb-4">Sorry, your session has expired. Please refresh and try again.</p>
                <div class="mb-4">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="fas fa-home"></i> Return to Home
                    </a>
                </div>
                <div class="mt-4">
                    <p class="text-muted">If the problem persists, please try logging in again.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 