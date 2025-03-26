@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.new-arrivals') }}" method="GET">
                        <!-- Search -->
                        <div class="mb-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search products...">
                        </div>

                        <!-- Categories -->
                        <div class="mb-3">
                            <label class="form-label">Categories</label>
                            @foreach($categories as $category)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="category{{ $category->id }}" value="{{ $category->id }}" {{ request('category') == $category->id ? 'checked' : '' }}>
                                <label class="form-check-label" for="category{{ $category->id }}">
                                    {{ $category->category_name }}
                                </label>
                            </div>
                            @endforeach
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
                <h2>New Arrivals</h2>
                <span>{{ $products->total() }} products found</span>
            </div>

            <div class="row">
                @forelse($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card product-card h-100">
                        <img src="{{ Storage::url($product->image) }}" class="card-img-top product-image" alt="{{ $product->title }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->title }}</h5>
                            <p class="card-text text-muted">{{ $product->category->category_name }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($product->discount_price)
                                    <span class="text-muted text-decoration-line-through">@baht($product->price)</span>
                                    <span class="text-danger ms-2">@baht($product->discount_price)</span>
                                    @else
                                    <span class="text-dark">@baht($product->price)</span>
                                    @endif
                                </div>
                                <div class="rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="{{ $i <= $product->average_rating ? 'fas fa-star' : 'far fa-star' }}"></i>
                                    @endfor
                                    @if($product->reviews->count() > 0)
                                        <span class="ms-1 small">{{ number_format($product->average_rating, 1) }} ({{ $product->reviews->count() }})</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-flex gap-2">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary flex-grow-1">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                                <form action="{{ route('cart.add', $product) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-karen w-100">
                                        <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        No new products found. Please try different filters.
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 