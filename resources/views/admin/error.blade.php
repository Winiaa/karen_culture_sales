@extends('layouts.admin')

@section('title', $title ?? 'Error')
@section('subtitle', 'Something went wrong')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body text-center py-5">
            <div class="mb-4">
                <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
            </div>
            <h2 class="mb-3">{{ $title ?? 'Error' }}</h2>
            <p class="lead mb-4">{{ $message ?? 'An unexpected error occurred.' }}</p>
            <a href="{{ $back_url ?? route('admin.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Go Back
            </a>
        </div>
    </div>
</div>
@endsection 