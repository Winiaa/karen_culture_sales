@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h1 class="mb-4">About Karen Culture Sales</h1>
            
            <div class="mb-5">
                <h2>Our Mission</h2>
                <p>At Karen Culture Sales, our mission is to preserve and promote the rich cultural heritage of the Karen people by providing a platform that connects skilled artisans with customers worldwide. We believe in empowering local communities while sharing the beauty of Karen craftsmanship with the global market.</p>
            </div>

            <div class="mb-5">
                <h2>Our Story</h2>
                <p>Founded in 2024, Karen Culture Sales began as a small initiative to help local Karen artisans showcase their traditional crafts. What started as a modest project has grown into a thriving marketplace that celebrates Karen culture and supports sustainable economic development within the community.</p>
            </div>

            <div class="mb-5">
                <h2>Our Values</h2>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Authenticity</h5>
                                <p class="card-text">We ensure that all products are authentically crafted by Karen artisans, maintaining traditional techniques and cultural significance.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Fair Trade</h5>
                                <p class="card-text">We practice fair trade principles, ensuring artisans receive fair compensation for their work and supporting sustainable economic growth.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Community Impact</h5>
                                <p class="card-text">A portion of our profits goes back to the Karen community, supporting education and cultural preservation initiatives.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Quality</h5>
                                <p class="card-text">We maintain high standards of quality, ensuring each product meets our strict criteria for craftsmanship and durability.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <h2>Our Impact</h2>
                <div class="row text-center">
                    <div class="col-md-4 mb-4">
                        <div class="h1 text-primary">100+</div>
                        <p>Artisans Supported</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="h1 text-primary">50+</div>
                        <p>Communities Reached</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="h1 text-primary">1000+</div>
                        <p>Products Sold</p>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">Shop Our Products</a>
            </div>
        </div>
    </div>
</div>
@endsection 