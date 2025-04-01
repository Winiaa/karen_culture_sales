@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>All Categories</h2>
        <span>{{ $categories->count() }} categories found</span>
    </div>

    <div class="row g-4">
        @forelse($categories as $category)
            <div class="col-md-4 mb-4">
                <a href="{{ route('categories.show', $category) }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm category-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="category-icon me-3">
                                    @if($category->icon)
                                        <i class="fas {{ $category->icon }} fa-2x text-primary"></i>
                                    @else
                                        <i class="fas fa-box fa-2x text-secondary"></i>
                                    @endif
                                </div>
                                <h3 class="h5 mb-0">{{ $category->category_name }}</h3>
                            </div>
                            <p class="text-muted mb-3">{{ $category->description ? Str::limit($category->description, 100) : 'No description available.' }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-box me-1"></i>
                                    {{ $category->products_count }} Products
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No categories found.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection 