<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Karen Culture Sales') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Styles -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        
        <!-- Page-specific styles -->
        @yield('styles')
        
        <style>
            :root {
                --primary-color: #1a472a; /* Dark green - main brand color */
                --primary-light: #2d634c; /* Lighter green for hover states */
                --primary-dark: #0e3019; /* Darker green for active states */
                --accent-color: #9caa64; /* Sage green accent */
                --accent-light: #d4ddb9; /* Light sage for hover states */
                --text-color: #333333;
                --text-light: #777777;
                --bg-light: #f3f7f4; /* Light sage background */
                --bg-medium: #e6ede8; /* Medium sage for sections */
                --white: #ffffff;
            }
            
            body {
                font-family: 'Poppins', sans-serif;
                color: var(--text-color);
                background-color: var(--bg-light);
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }
            
            main {
                flex: 1;
            }
            
            .navbar {
                background-color: var(--primary-color);
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            
            .navbar-brand {
                color: var(--white);
                font-weight: 700;
            }
            
            .navbar-dark .navbar-nav .nav-link {
                color: rgba(255, 255, 255, 0.85);
                font-weight: 500;
                transition: color 0.3s;
            }
            
            .navbar-dark .navbar-nav .nav-link:hover {
                color: var(--white);
            }
            
            .navbar-dark .navbar-nav .nav-link.active {
                color: var(--white);
                font-weight: 600;
                border-bottom: 2px solid var(--accent-color);
            }
            
            .dropdown-menu {
                border: none;
                border-radius: 0.5rem;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                overflow: hidden;
            }
            
            .dropdown-item {
                padding: 0.5rem 1rem;
                transition: all 0.2s;
            }
            
            .dropdown-item:hover {
                background-color: var(--bg-light);
                color: var(--primary-color);
            }
            
            .dropdown-item:active {
                background-color: var(--primary-color);
                color: var(--white);
            }
            
            .dropdown-divider {
                margin: 0.5rem 0;
                border-top: 1px solid var(--bg-medium);
            }
            
            .bg-primary {
                background-color: var(--primary-color) !important;
            }
            
            .bg-accent {
                background-color: var(--accent-color) !important;
                color: var(--white);
            }
            
            .btn-primary {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
            }
            
            .btn-primary:hover {
                background-color: var(--primary-light);
                border-color: var(--primary-light);
            }
            
            .btn-karen {
                background-color: #38603f;
                color: white;
                border: none;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }
            
            .btn-karen:hover {
                background-color: #4b7a53;
                color: white;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            
            .btn-karen:active {
                transform: translateY(0);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            
            .btn-karen::after {
                content: '';
                position: absolute;
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                background-color: rgba(255, 255, 255, 0.3);
                transform: scaleX(0);
                transform-origin: right;
                transition: transform 0.3s ease;
            }
            
            .btn-karen:hover::after {
                transform: scaleX(1);
                transform-origin: left;
            }
            
            .btn-karen.added {
                background-color: #28a745;
                color: white;
                box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
                animation: addedPulse 0.5s ease;
            }
            
            @keyframes addedPulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
            
            .btn-outline-karen {
                border-color: var(--primary-color);
                color: var(--primary-color);
            }
            
            .btn-outline-karen:hover {
                background-color: var(--primary-color);
                color: var(--white);
            }
            
            .hero-section {
                background: linear-gradient(135deg, var(--bg-light) 0%, var(--bg-medium) 100%);
            }
            
            .feature-icon, .category-icon {
                background-color: rgba(26, 71, 42, 0.1);
            }
            
            .card {
                border-radius: 12px;
                transition: transform 0.3s, box-shadow 0.3s;
            }
            
            .card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            }
            
            footer {
                background-color: var(--primary-color);
                color: var(--white);
                padding: 3rem 0;
            }
            
            footer a {
                color: var(--accent-light);
                text-decoration: none;
            }
            
            footer a:hover {
                color: var(--white);
                text-decoration: underline;
            }
            
            /* Alert styling */
            .alert {
                border-radius: 10px;
                border: none;
            }
            
            .alert-success {
                background-color: #d1e7dd;
                color: #0f5132;
            }
            
            .alert-danger {
                background-color: #f8d7da;
                color: #842029;
            }
            
            /* Badges */
            .badge {
                font-weight: 500;
                padding: 0.35em 0.65em;
            }
            
            /* Category and product cards */
            .category-card .card-body {
                padding: 1.5rem;
            }
            
            /* Product listing */
            .product-card {
                height: 100%;
                transition: all 0.3s ease;
            }
            
            .product-card .card-img-top {
                height: 200px;
                object-fit: cover;
            }
            
            /* Responsive adjustments for product cards */
            @media (max-width: 767.98px) {
                .product-card .card-img-top {
                    height: 180px;
                }
                
                .product-card .card-title {
                    font-size: 1rem;
                }
                
                .product-card .rating {
                    font-size: 0.9rem;
                }
                
                .product-card .badge {
                    font-size: 0.75rem;
                }
            }
            
            @media (max-width: 575.98px) {
                .product-card .card-img-top {
                    height: 160px;
                }
                
                .product-card .card-body {
                    padding: 0.75rem;
                }
                
                .product-card .card-footer {
                    padding: 0.75rem;
                }
                
                .product-card .btn {
                    padding: 0.375rem 0.75rem;
                    font-size: 0.875rem;
                }
            }
            
            /* Price styling */
            .price {
                font-weight: 600;
                color: var(--primary-color);
            }
            
            .original-price {
                text-decoration: line-through;
                color: var(--text-light);
                font-size: 0.9em;
            }
            
            /* Custom section backgrounds */
            .bg-karen-light {
                background-color: var(--bg-light);
            }
            
            .bg-karen-medium {
                background-color: var(--bg-medium);
            }
            
            .bg-karen-primary {
                background-color: var(--primary-color);
                color: var(--white);
            }
            
            .bg-karen-accent {
                background-color: var(--accent-color);
                color: var(--white);
            }
            
            /* Custom pagination styling */
            .pagination .page-item.active .page-link {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
            }
            
            .pagination .page-link {
                color: var(--primary-color);
            }
            
            /* Star Rating Styles */
            .fas.fa-star {
                color: #FFD700 !important; /* Yellow gold color for filled stars */
            }
            
            .fas.fa-star-o, 
            .far.fa-star {
                color: #CCCCCC !important; /* Light gray for empty stars */
            }
            
            /* Rating display in product cards */
            .rating {
                display: inline-flex;
                align-items: center;
                gap: 2px;
            }
            
            /* Enhanced star styling for product cards */
            .product-card .rating .fas.fa-star,
            .rating .fas.fa-star {
                color: #FFC107 !important; /* Amber color for filled stars */
                text-shadow: 0 0 2px rgba(255, 193, 7, 0.4);
                transform: scale(1.1);
                transition: all 0.2s ease;
            }
            
            .product-card .rating .fas.fa-star-o,
            .product-card .rating .far.fa-star,
            .rating .fas.fa-star-o,
            .rating .far.fa-star {
                color: #D0D0D0 !important; /* Slightly darker gray for empty stars to ensure visibility */
                text-shadow: none;
                transition: all 0.2s ease;
                opacity: 1 !important; /* Force full opacity to ensure visibility */
            }
            
            /* Blinking animation for unrated stars */
            @keyframes starBlink {
                0% { opacity: 0.7; }
                50% { opacity: 1; }
                100% { opacity: 0.7; }
            }
            
            /* Apply animation to unrated products */
            .product-card .rating .fas.fa-star-o,
            .product-card .rating .far.fa-star,
            .rating .fas.fa-star-o,
            .rating .far.fa-star {
                animation: starBlink 3s infinite;
            }
            
            /* Star container effect - REMOVED */
            .rating {
                display: inline-flex;
                align-items: center;
            }
            
            /* Rating count */
            .rating .small {
                font-size: 0.75rem;
                color: #666;
                font-weight: 500;
            }
            
            /* For hover effect on rating selection */
            .rating-select .fas.fa-star,
            .rating-star {
                cursor: pointer;
            }
            
            /* Make all rating displays inline */
            .rating, .rating-input {
                display: inline-flex !important;
                align-items: center !important;
            }
            
            /* Maintain star spacing */
            .rating-star {
                padding-right: 4px;
            }
            
            /* Animation for star selection */
            @keyframes starPulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.2); }
                100% { transform: scale(1); }
            }
            
            /* Apply animation when star is selected */
            .star-selected {
                animation: starPulse 0.3s ease-in-out;
            }
            
            /* Make sure all stars are clickable */
            .rating-star {
                cursor: pointer;
                user-select: none;
            }
            
            /* Add to Cart button hover effect */
            .card-footer .btn-karen {
                transition: all 0.3s ease;
            }
            
            .card-footer .btn-karen:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            }
            
            /* Improve product card hover effect */
            .product-card {
                transition: all 0.3s ease;
            }
            
            .product-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
            }
        </style>
        
        @stack('styles')
    </head>
    <body>
        @include('layouts.navigation')

        <main>
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="py-5 mt-auto">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-4 mb-4 mb-lg-0">
                        <h5 class="fw-bold mb-3">Karen Culture Sales</h5>
                        <p class="opacity-75">Preserving and promoting the rich cultural heritage of the Karen people through authentic handcrafted products.</p>
                        <div class="d-flex gap-3 mt-4">
                            <a href="#" class="text-decoration-none">
                                <i class="fab fa-facebook-f fa-lg"></i>
                            </a>
                            <a href="#" class="text-decoration-none">
                                <i class="fab fa-instagram fa-lg"></i>
                            </a>
                            <a href="#" class="text-decoration-none">
                                <i class="fab fa-twitter fa-lg"></i>
                            </a>
                            <a href="#" class="text-decoration-none">
                                <i class="fab fa-pinterest fa-lg"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-6 col-lg-2 mb-4 mb-lg-0">
                        <h6 class="fw-bold mb-3">Shop</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><a href="{{ route('products.index') }}" class="text-decoration-none">All Products</a></li>
                            <li class="mb-2"><a href="{{ route('categories.index') }}" class="text-decoration-none">Categories</a></li>
                            <li class="mb-2"><a href="{{ route('products.new-arrivals') }}" class="text-decoration-none">New Arrivals</a></li>
                            <li class="mb-2"><a href="{{ route('products.best-sellers') }}" class="text-decoration-none">Best Sellers</a></li>
                        </ul>
                    </div>
                    <div class="col-6 col-lg-2 mb-4 mb-lg-0">
                        <h6 class="fw-bold mb-3">About</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><a href="{{ route('about') }}" class="text-decoration-none">Our Story</a></li>
                            <li class="mb-2"><a href="{{ route('artisans') }}" class="text-decoration-none">Artisans</a></li>
                            <li class="mb-2"><a href="{{ route('contact') }}" class="text-decoration-none">Contact Us</a></li>
                            <li class="mb-2"><a href="{{ route('faq') }}" class="text-decoration-none">FAQ</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4">
                        <h6 class="fw-bold mb-3">Newsletter</h6>
                        <p class="opacity-75">Subscribe to receive updates and special offers.</p>
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Your email address">
                            <button class="btn btn-light" type="button">Subscribe</button>
                        </div>
                    </div>
                </div>
                <hr class="my-4 opacity-25">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <span class="opacity-75">&copy; {{ date('Y') }} Karen Culture Sales. All rights reserved.</span>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item"><a href="{{ route('privacy') }}" class="text-decoration-none opacity-75">Privacy Policy</a></li>
                            <li class="list-inline-item ms-3"><a href="{{ route('terms') }}" class="text-decoration-none opacity-75">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            // Enable Bootstrap tooltips
            document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
            
            // Setup CSRF handling for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        </script>
        
        @stack('scripts')
    </body>
</html>
