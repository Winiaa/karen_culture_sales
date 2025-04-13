@extends('layouts.admin')

@section('title', '429 - Too Many Requests')
@section('subtitle', 'You have made too many requests.')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="error-page">
                <h1 class="display-1 text-danger">429</h1>
                <p class="lead mb-4">Too Many Requests</p>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-tachometer-alt"></i> Return to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 