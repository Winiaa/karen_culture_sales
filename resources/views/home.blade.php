@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="hero-section py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold mb-4">Discover Karen Cultural Heritage</h1>
                <p class="lead mb-4">Explore our curated collection of authentic Karen cultural products, handcrafted with traditional techniques and passed down through generations.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('products.index') }}" class="btn btn-karen btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Shop Now
                    </a>
                    <a href="{{ route('about') }}" class="btn btn-outline-karen btn-lg">
                        <i class="fas fa-info-circle me-2"></i>Learn More
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-4 bg-white rounded-3 shadow-sm">
                    <img src="{{ asset('images/hero-image.jpg') }}" alt="Karen Cultural Products" class="img-fluid rounded-3">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Categories -->
<section class="mb-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Shop by Category</h2>
            <a href="{{ route('categories.index') }}" class="btn btn-link text-decoration-none text-primary">
                View All Categories <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        <div class="row g-4">
            @foreach($categories as $category)
                <div class="col-md-4">
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
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="mb-5 bg-karen-medium py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Featured Products</h2>
            <a href="{{ route('products.index') }}" class="btn btn-link text-decoration-none text-primary">
                View All Products <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        <div class="row g-4">
            @foreach($featuredProducts as $product)
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm product-card">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top product-image" alt="{{ $product->title }}">
                        @else
                            <div class="card-img-top bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        @endif
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
                                <div class="rating mb-2">
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
            @endforeach
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="mb-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-truck-fast fa-3x text-primary"></i>
                        </div>
                        <h3 class="h5 mb-3">Fast Delivery</h3>
                        <p class="text-muted mb-0">Free shipping on orders over $50. Quick and secure delivery to your doorstep.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-certificate fa-3x text-success"></i>
                        </div>
                        <h3 class="h5 mb-3">Authentic Products</h3>
                        <p class="text-muted mb-0">Each item is handcrafted by skilled Karen artisans using traditional techniques.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-heart fa-3x text-danger"></i>
                        </div>
                        <h3 class="h5 mb-3">Support Artisans</h3>
                        <p class="text-muted mb-0">Your purchase directly supports Karen artisans and helps preserve their cultural heritage.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="bg-karen-primary py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h2 class="mb-4 text-white">Stay Updated</h2>
                <p class="lead mb-4 text-white opacity-75">Subscribe to our newsletter for updates on new products, cultural events, and exclusive offers.</p>
                <form class="row g-3 justify-content-center">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="email" class="form-control form-control-lg" placeholder="Enter your email">
                            <button class="btn btn-light btn-lg" type="submit">Subscribe</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .text-primary {
        color: var(--primary-color) !important;
    }
    
    .text-success {
        color: #2d8a62 !important;
    }
    
    .hero-section {
        position: relative;
        background: linear-gradient(135deg, rgba(26, 71, 42, 0.1) 0%, rgba(26, 71, 42, 0.2) 100%);
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%231a472a' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.5;
        z-index: 0;
    }
    
    .hero-section .container {
        position: relative;
        z-index: 1;
    }
    
    .category-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(26, 71, 42, 0.1) 0%, rgba(26, 71, 42, 0.2) 100%);
    }
    
    .btn-karen {
        background: linear-gradient(to right, var(--primary-color), var(--primary-light));
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-karen:hover {
        background: linear-gradient(to right, var(--primary-light), var(--primary-color));
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .btn-outline-karen {
        background: transparent;
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        transition: all 0.3s ease;
    }
    
    .btn-outline-karen:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .feature-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        background: linear-gradient(135deg, rgba(26, 71, 42, 0.1) 0%, rgba(26, 71, 42, 0.2) 100%);
    }
</style>
@endpush 