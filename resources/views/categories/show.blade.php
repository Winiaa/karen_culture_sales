@extends('layouts.app')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categories</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-md-12">
            <h1>{{ $category->name }}</h1>
            @if($category->description)
                <p class="lead">{{ $category->description }}</p>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.show', $category) }}" method="GET">
                        <!-- Search -->
                        <div class="mb-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search products...">
                        </div>

                        <!-- Sort -->
                        <div class="mb-3">
                            <label for="sort" class="form-label">Sort By</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-karen">Apply Filters</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>{{ $category->name }} Products</h2>
                <span>{{ $products->total() }} products found</span>
            </div>
            
            <div class="row">
                @forelse($products as $product)
                    <div class="col-12 col-md-6 col-lg-4 mb-4">
                        <x-product-card :product="$product" />
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            No products found in this category.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="row">
                <div class="col-12">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 