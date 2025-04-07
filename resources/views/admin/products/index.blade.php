@extends('layouts.admin')

@section('title', 'Products')
@section('subtitle', 'Manage your product inventory')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products Management</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Product
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Products List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="productsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/no-image.png') }}" alt="No Image" style="width: 50px; height: 50px; object-fit: cover;">
                                    @endif
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td>฿{{ number_format($product->price, 2) }}</td>
                                <td>
                                    @php
                                        $stockColor = 'success';
                                        $stockText = $product->quantity;
                                        
                                        if ($product->quantity <= 0) {
                                            $stockColor = 'danger';
                                            $stockText = 'Out of Stock';
                                        } elseif ($product->quantity <= 5) {
                                            $stockColor = 'warning';
                                            $stockText = $product->quantity . ' (Low)';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $stockColor }}">
                                        {{ $stockText }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $product->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-info btn-sm" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm delete-btn" 
                                                title="Delete"
                                                data-delete-url="{{ route('admin.products.destroy', $product) }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Include the delete confirmation modal -->
<x-delete-confirmation-modal />
@endsection

@push('styles')
<style>
    .btn-group .btn {
        margin-right: 2px;
    }
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    .badge {
        padding: 0.5em 0.75em;
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