@extends('layouts.admin')

@section('title', $category->name)
@section('subtitle', 'View category details')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">Category Details</h5>
            </div>
            <div class="col text-end">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted mb-3">Basic Information</h6>
                
                <div class="mb-4">
                    <h4 class="mb-0">{{ $category->name }}</h4>
                    <div class="text-muted">Name</div>
                </div>
                
                <div class="mb-4">
                    <div class="badge bg-{{ $category->is_active ? 'success' : 'danger' }} mb-2">
                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                    </div>
                    <div class="text-muted">Status</div>
                </div>
                
                <div class="mb-4">
                    <p class="mb-0">{{ $category->description ?? 'No description provided' }}</p>
                    <div class="text-muted">Description</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted mb-3">Statistics</h6>
                
                <div class="mb-4">
                    <h4 class="mb-0">{{ $category->products_count }}</h4>
                    <div class="text-muted">Products in this category</div>
                </div>
                
                <div class="mb-4">
                    <h4 class="mb-0">{{ $category->created_at->format('M d, Y') }}</h4>
                    <div class="text-muted">Created at</div>
                </div>
                
                <div class="mb-4">
                    <h4 class="mb-0">{{ $category->updated_at->format('M d, Y') }}</h4>
                    <div class="text-muted">Last updated</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($category->products_count > 0)
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Products in this Category</h5>
        <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i> Add Product
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($category->products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->title }}" 
                                     class="rounded" width="40" height="40" style="object-fit: cover;">
                                @else
                                <div class="bg-light rounded p-2">
                                    <i class="fas fa-box text-secondary"></i>
                                </div>
                                @endif
                                <div class="ms-3">
                                    <h6 class="mb-0">{{ $product->title }}</h6>
                                    <small class="text-muted">SKU: {{ $product->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>@baht($product->price)</td>
                        <td>{{ $product->quantity ?? 0 }}</td>
                        <td>
                            <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="alert alert-info mt-4">
    <i class="fas fa-info-circle me-2"></i> This category doesn't have any products yet.
    <a href="{{ route('admin.products.create') }}" class="alert-link">Add a product</a> to this category.
</div>
@endif
@endsection 