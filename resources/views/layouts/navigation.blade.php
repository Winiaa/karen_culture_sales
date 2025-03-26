<!-- Primary Navigation Menu -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <i class="fas fa-leaf me-2"></i>
            <span>Karen Culture Sales</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="fas fa-home me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('products.*') || request()->routeIs('categories.*') ? 'active' : '' }}" href="#" id="shopDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-shopping-bag me-1"></i> Shop
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="shopDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('products.index') }}">
                                <i class="fas fa-tags me-2"></i>All Products
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('categories.index') }}">
                                <i class="fas fa-th-large me-2"></i>Categories
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('products.new-arrivals') }}">
                                <i class="fas fa-calendar-plus me-2"></i>New Arrivals
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('products.best-sellers') }}">
                                <i class="fas fa-fire-alt me-2"></i>Best Sellers
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('about') || request()->routeIs('artisans') || request()->routeIs('faq') ? 'active' : '' }}" href="#" id="aboutDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-info-circle me-1"></i> About Us
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="aboutDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('about') }}">
                                <i class="fas fa-book-open me-2"></i>Our Story
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('artisans') }}">
                                <i class="fas fa-hands me-2"></i>Artisans
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('faq') }}">
                                <i class="fas fa-question-circle me-2"></i>FAQ
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                        <i class="fas fa-envelope me-1"></i> Contact Us
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tracking.*') ? 'active' : '' }}" href="{{ route('tracking.index') }}">
                        <i class="fas fa-shipping-fast me-1"></i> Track
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link position-relative {{ request()->routeIs('cart.index') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                        <i class="fas fa-shopping-cart me-1"></i> Cart
                        @auth
                            <span id="cart-count" class="badge bg-accent position-absolute top-0 start-100 translate-middle rounded-pill">
                                {{ App\Models\Cart::where('user_id', Auth::id())->sum('quantity') }}
                            </span>
                        @endauth
                    </a>
                </li>
                
                @guest
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i> Register
                        </a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ Auth::user()->profile_picture_url }}" alt="{{ Auth::user()->name }}" class="rounded-circle me-1" 
                                 style="width: 30px; height: 30px; object-fit: cover; border: 2px solid white;">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user me-2"></i>Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('orders.index') }}">
                                    <i class="fas fa-box me-2"></i>My Orders
                                </a>
                            </li>
                            @if (Auth::user()->usertype === 'admin')
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                                    </a>
                                </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" data-turbo="false">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
