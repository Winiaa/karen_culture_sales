@extends('layouts.admin')

@section('title', 'Edit Product')
@section('subtitle', 'Update product information')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Product</h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Products
        </a>
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
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Product Details</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Product Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $product->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">฿</span>
                                        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" value="{{ old('price', $product->price) }}" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="discount_price" class="form-label">Discount Price (Optional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">฿</span>
                                        <input type="number" step="0.01" class="form-control @error('discount_price') is-invalid @enderror" 
                                               id="discount_price" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}">
                                        @error('discount_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">Leave empty if no discount is available.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Stock</label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                           id="quantity" name="quantity" value="{{ old('quantity', $product->quantity) }}" required>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($product->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->title }}" 
                                         class="img-thumbnail main-product-image">
                                </div>
                            @endif
                        </div>

                        <!-- Additional Images Section -->
                        <div class="mb-3">
                            <label for="additional_images" class="form-label">Additional Images</label>
                            <input type="file" class="form-control @error('additional_images') is-invalid @enderror" 
                                   id="additional_images" name="additional_images[]" accept="image/*" multiple>
                            @error('additional_images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">You can select multiple images. Each image should be less than 2MB.</div>
                            
                            @if(!empty($product->additional_images))
                                <div class="mt-3">
                                    <h6>Current Additional Images:</h6>
                                    <div class="additional-images-grid">
                                        @foreach($product->additional_images as $index => $imagePath)
                                            <div class="additional-image-item">
                                                <div class="position-relative image-container">
                                                    <img src="{{ asset('storage/' . $imagePath) }}" 
                                                         alt="Additional Image {{ $index + 1 }}" 
                                                         class="img-thumbnail additional-image">
                                                    <div class="delete-badge" data-image-path="{{ $imagePath }}" 
                                                         data-image-index="{{ $index }}">
                                                        <i class="fas fa-trash"></i>
                                                    </div>
                                                    <input type="hidden" name="delete_images[]" value="{{ $imagePath }}" 
                                                           id="delete_image_{{ $index }}" class="delete-image-input">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle delete image badges
        const deleteBadges = document.querySelectorAll('.delete-badge');
        
        // Initialize all delete inputs to empty
        deleteBadges.forEach(badge => {
            const imageIndex = badge.getAttribute('data-image-index');
            const inputField = document.getElementById(`delete_image_${imageIndex}`);
            inputField.value = ''; // Ensure all inputs start empty
        });
        
        // Add click event to each badge
        deleteBadges.forEach(badge => {
            badge.addEventListener('click', function() {
                const imagePath = this.getAttribute('data-image-path');
                const imageIndex = this.getAttribute('data-image-index');
                const inputField = document.getElementById(`delete_image_${imageIndex}`);
                
                // Toggle the active state of this badge only
                if (this.classList.contains('active')) {
                    // Unmark for deletion
                    inputField.value = '';
                    this.classList.remove('active');
                } else {
                    // Mark for deletion
                    inputField.value = imagePath;
                    this.classList.add('active');
                }
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    .img-thumbnail {
        max-width: 100%;
        height: auto;
    }
    .alert {
        border-radius: 0.35rem;
        margin-bottom: 1.5rem;
    }
    .alert .btn-close {
        padding: 1.25rem;
    }
    .main-product-image {
        max-height: 200px;
        width: auto;
        display: block;
        margin-bottom: 1rem;
    }
    
    /* Additional Images Grid Layout */
    .additional-images-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 2px;
        margin-bottom: 1rem;
    }
    
    .additional-image-item {
        width: 100%;
    }
    
    .image-container {
        position: relative;
        overflow: hidden;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        width: 100%;
        padding-bottom: 100%; /* Creates a square aspect ratio */
        margin: 0;
    }
    
    .additional-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .delete-badge {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 24px;
        height: 24px;
        background-color: rgba(13, 110, 253, 0.9);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 10px;
        transition: all 0.2s;
        z-index: 10;
        opacity: 0;
    }
    
    .image-container:hover .delete-badge {
        opacity: 1;
    }
    
    .delete-badge:hover {
        background-color: #0d6efd;
        transform: translate(-50%, -50%) scale(1.1);
    }
    
    .delete-badge.active {
        background-color: #dc3545;
        opacity: 1;
    }
</style>
@endpush 