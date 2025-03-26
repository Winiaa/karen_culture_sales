@extends('layouts.admin')

@section('title', 'Add New Category')
@section('subtitle', 'Create a new product category')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">Add New Category</h5>
            </div>
            <div class="col text-end">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Categories
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="category_name" class="form-label">Category Name</label>
                <input type="text" class="form-control @error('category_name') is-invalid @enderror" 
                       id="category_name" name="category_name" value="{{ old('category_name') }}" required>
                @error('category_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Provide a brief description of this category</div>
            </div>
            
            <div class="mb-4">
                <label for="icon" class="form-label">Icon (Font Awesome)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-icons"></i></span>
                    <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                           id="icon" name="icon" value="{{ old('icon') }}" placeholder="fa-tshirt">
                </div>
                @error('icon')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    Enter a Font Awesome icon name (e.g., fa-tshirt, fa-gem). 
                    <a href="https://fontawesome.com/icons" target="_blank">Browse icons</a>
                </div>
                <div class="mt-2">
                    <span class="me-2">Common icons:</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary icon-option me-1 mb-1" data-icon="fa-tshirt">
                        <i class="fas fa-tshirt"></i> Clothing
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary icon-option me-1 mb-1" data-icon="fa-gem">
                        <i class="fas fa-gem"></i> Jewelry
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary icon-option me-1 mb-1" data-icon="fa-home">
                        <i class="fas fa-home"></i> Home
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary icon-option me-1 mb-1" data-icon="fa-utensils">
                        <i class="fas fa-utensils"></i> Food
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary icon-option me-1 mb-1" data-icon="fa-gift">
                        <i class="fas fa-gift"></i> Gift
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary icon-option me-1 mb-1" data-icon="fa-scroll">
                        <i class="fas fa-scroll"></i> Textile
                    </button>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" 
                           id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
                <div class="form-text">Inactive categories will not be shown to customers</div>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Category
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Icon selection
        const iconOptions = document.querySelectorAll('.icon-option');
        const iconInput = document.getElementById('icon');
        
        iconOptions.forEach(option => {
            option.addEventListener('click', function() {
                const icon = this.dataset.icon;
                iconInput.value = icon;
                
                // Update all buttons to show which one is selected
                iconOptions.forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-secondary');
                });
                
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-primary');
            });
        });
    });
</script>
@endpush
@endsection 