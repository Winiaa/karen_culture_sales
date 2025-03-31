@extends('layouts.admin')

@section('title', 'Product Details')
@section('subtitle', 'View product information')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Product Details</h1>
        <div>
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit"></i> Edit Product
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Product Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                                     class="img-fluid rounded mb-3">
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h4 class="mb-3">{{ $product->name }}</h4>
                            <p class="text-muted mb-4">{{ $product->description }}</p>
                            
                            <div class="mb-3">
                                <strong>Price:</strong>
                                <span class="h5 text-primary">${{ number_format($product->price, 2) }}</span>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Stock:</strong>
                                @php
                                    $stockColor = 'success';
                                    $stockText = $product->quantity;
                                    
                                    if ($product->quantity <= 0) {
                                        $stockColor = 'danger';
                                        $stockText = 'Out of Stock';
                                    } elseif ($product->quantity <= 5) {
                                        $stockColor = 'warning';
                                        $stockText = $product->quantity . ' (Low Stock)';
                                    }
                                @endphp
                                <span class="badge bg-{{ $stockColor }}">
                                    {{ $stockText }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Category:</strong>
                                <span>{{ $product->category->name }}</span>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Status:</strong>
                                <span class="badge bg-{{ $product->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Product Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted mb-2">Total Orders</h6>
                                <h3 class="mb-0">{{ $product->orders_count }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted mb-2">Total Revenue</h6>
                                <h3 class="mb-0">${{ number_format($product->total_revenue, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted mb-2">Average Rating</h6>
                                <h3 class="mb-0">{{ number_format($product->average_rating, 1) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-danger delete-btn" 
                                data-delete-url="{{ route('admin.products.destroy', $product) }}">
                            <i class="fas fa-trash"></i> Delete Product
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include the delete confirmation modal -->
<x-delete-confirmation-modal />
@endsection

@push('styles')
<style>
    .img-fluid {
        max-height: 300px;
        width: auto;
    }
    .alert {
        border-radius: 0.35rem;
        margin-bottom: 1.5rem;
    }
    .alert .btn-close {
        padding: 1.25rem;
    }
</style>
@endpush 