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
                    <form action="{{ route('products.best-sellers') }}" method="GET">
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

                        <!-- Sort -->
                        <div class="mb-3">
                            <label for="sort" class="form-label">Sort By</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
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
                <h2>Best Sellers</h2>
                <span>{{ $products->total() }} products found</span>
            </div>

            <div class="row">
                @forelse($products as $product)
                <div class="col-12 col-md-6 col-lg-4 mb-4">
                    <div class="position-relative">
                        @if(isset($topBestSellerIds) && in_array($product->id, $topBestSellerIds))
                            @php $rank = array_search($product->id, $topBestSellerIds) + 1; @endphp
                            <div class="position-absolute top-0 start-0 m-2 z-1">
                                @if($rank == 1)
                                    <span class="badge bg-warning text-dark">#1 Best Seller</span>
                                @elseif($rank == 2)
                                    <span class="badge bg-secondary">#2 Best Seller</span>
                                @elseif($rank == 3)
                                    <span class="badge bg-danger">#3 Best Seller</span>
                                @endif
                            </div>
                        @elseif($product->order_count >= 5)
                            <div class="position-absolute top-0 start-0 m-2 z-1">
                                <span class="badge bg-danger">Best Seller</span>
                            </div>
                        @endif
                        <x-product-card :product="$product" />
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        No best selling products found. Please try different filters or check back later.
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
