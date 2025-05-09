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
        @vite(['resources/css/app.css'])
        
        <!-- Page-specific styles -->
        @yield('styles')
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

        <footer class="footer py-5 mt-auto">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-4 mb-4 mb-lg-0">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-leaf fa-2x me-2"></i>
                            <h5 class="fw-bold mb-0">Karen Culture Sales</h5>
                        </div>
                        <p class="opacity-75 mb-4">Preserving and promoting the rich cultural heritage of the Karen people through authentic handcrafted products.</p>
                        <div class="footer-contact">
                            <a href="mailto:info@karenculturesales.com" class="d-flex align-items-center mb-3 text-decoration-none">
                                <i class="fas fa-envelope me-3"></i>
                                <span>info@karenculturesales.com</span>
                            </a>
                            <a href="tel:+1234567890" class="d-flex align-items-center text-decoration-none">
                                <i class="fas fa-phone me-3"></i>
                                <span>+1 (234) 567-890</span>
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
                        <h6 class="fw-bold mb-3">Connect With Us</h6>
                        <p class="opacity-75 mb-4">Follow us on social media for the latest updates and cultural insights.</p>
                        <div class="social-links d-flex gap-3">
                            <a href="#" class="social-link" title="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-link" title="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-link" title="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-link" title="Pinterest">
                                <i class="fab fa-pinterest"></i>
                            </a>
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

<style>
.footer {
    background-color: var(--primary-color);
    color: var(--white);
}

.footer a {
    color: var(--white);
    opacity: 0.8;
    transition: all 0.3s ease;
}

.footer a:hover {
    opacity: 1;
    transform: translateX(5px);
}

.footer .fab {
    transition: all 0.3s ease;
}

.footer .fab:hover {
    transform: translateY(-3px);
}

.footer-contact a {
    color: var(--white);
    opacity: 0.8;
    transition: all 0.3s ease;
}

.footer-contact a:hover {
    opacity: 1;
    transform: translateX(5px);
}

.footer-contact i {
    width: 20px;
    font-size: 1.1rem;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.1);
    color: var(--white);
    transition: all 0.3s ease;
}

.social-link:hover {
    background-color: var(--accent-light);
    color: var(--white);
    transform: translateY(-3px);
}

.social-link i {
    font-size: 1.2rem;
}

.footer-heading {
    position: relative;
    padding-bottom: 0.5rem;
}

.footer-heading::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 30px;
    height: 2px;
    background-color: var(--accent-light);
}
</style>
