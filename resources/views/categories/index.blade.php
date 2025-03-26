@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">All Categories</h1>

    <div class="row g-4">
        @foreach($categories as $category)
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
                                <h3 class="h5 mb-0">{{ $category->name }}</h3>
                            </div>
                            <p class="text-muted mb-0">{{ Str::limit($category->description, 100) }}</p>
                            <p class="text-muted mt-2">
                                <small>{{ $category->products_count ?? $category->products->count() }} Products</small>
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection 