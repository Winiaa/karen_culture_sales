<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Karen Culture') }} | Driver</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
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
        }
        
        .driver-sidebar {
            background-color: var(--primary-color);
            min-height: 100vh;
            color: white;
        }
        
        .driver-sidebar .nav-link {
            color: #f8f9fa;
            padding: 0.75rem 1.25rem;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
        }
        
        .driver-sidebar .nav-link:hover {
            background-color: var(--primary-light);
        }
        
        .driver-sidebar .nav-link.active {
            background-color: var(--accent-color);
            color: var(--primary-dark);
            font-weight: 500;
        }
        
        .driver-sidebar .sidebar-brand {
            padding: 1.5rem 1.25rem;
            font-size: 1.25rem;
            font-weight: 600;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .stats-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .bg-warning {
            background-color: #f0ad4e !important;
        }
        
        .bg-success {
            background-color: #4caf50 !important;
        }
        
        .bg-danger {
            background-color: #f44336 !important;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
        }
        
        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .driver-sidebar {
                min-height: auto;
            }
            
            .content-wrapper {
                margin-left: 0 !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 p-0 driver-sidebar d-md-block collapse" id="driverSidebar">
                <div class="d-flex flex-column min-vh-100">
                    <div class="p-3 text-center">
                        <h5 class="text-white">{{ config('app.name', 'Karen Culture Sales') }}</h5>
                        <h6 class="text-white-50">Driver Portal</h6>
                    </div>
                    
                    <!-- User profile section -->
                    <div class="user-profile text-center py-3">
                        <div class="mb-2">
                            <img src="{{ Auth::user()->profile_picture_url }}" alt="{{ Auth::user()->name }}" 
                                 class="img-fluid rounded-circle border border-2 border-light" 
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <h6 class="mb-0 text-white">{{ Auth::user()->name }}</h6>
                        <p class="text-white-50 small mb-1">Driver</p>
                        <span class="badge {{ auth()->user()->driver && auth()->user()->driver->is_active ? 'bg-success' : 'bg-secondary' }} mb-2">
                            {{ auth()->user()->driver && auth()->user()->driver->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    
                    <hr class="bg-light my-2">
                    
                    <ul class="nav flex-column mb-auto">
                        <li class="nav-item">
                            <a href="{{ route('driver.dashboard') }}" class="nav-link {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('driver.deliveries.index') }}" class="nav-link {{ request()->routeIs('driver.deliveries.index') ? 'active' : '' }}">
                                <i class="fas fa-list"></i> All Deliveries
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('driver.deliveries.assigned') }}" class="nav-link {{ request()->routeIs('driver.deliveries.assigned') ? 'active' : '' }}">
                                <i class="fas fa-truck-loading"></i> Assigned Deliveries
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('driver.deliveries.completed') }}" class="nav-link {{ request()->routeIs('driver.deliveries.completed') ? 'active' : '' }}">
                                <i class="fas fa-check-circle"></i> Completed Deliveries
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs('driver.profile.*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#profileSubmenu">
                                <i class="fas fa-user-circle"></i> My Account
                            </a>
                            <div class="collapse {{ request()->routeIs('driver.profile.*') ? 'show' : '' }}" id="profileSubmenu">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a href="{{ route('driver.profile.edit') }}" class="nav-link {{ request()->routeIs('driver.profile.edit') ? 'active' : '' }}">
                                            <i class="fas fa-id-card"></i> Driver Profile
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('driver.profile.password') }}" class="nav-link {{ request()->routeIs('driver.profile.password') ? 'active' : '' }}">
                                            <i class="fas fa-key"></i> Change Password
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                    <hr class="bg-light">
                    
                    <!-- Spacer to push logout to bottom -->
                    <div class="flex-grow-1"></div>
                    
                    <!-- Logout button at very bottom of sidebar -->
                    <div class="px-3 py-3 mt-auto">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 driver-content">
                <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 shadow-sm rounded">
                    <div class="container-fluid">
                        <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#driverSidebar">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <span class="navbar-brand d-none d-sm-inline">
                            @if (auth()->user() && auth()->user()->driver)
                                <span class="badge {{ auth()->user()->driver->is_active ? 'bg-success' : 'bg-secondary' }} me-2">
                                    {{ auth()->user()->driver->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                {{ auth()->user()->name }}
                            @endif
                        </span>
                        <div class="ms-auto">
                            <span class="text-secondary">
                                <i class="fas fa-calendar-alt me-1"></i> {{ date('F j, Y') }}
                            </span>
                        </div>
                    </div>
                </nav>
                
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show mb-4">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <!-- Page Content -->
                <div class="bg-white p-4 shadow-sm rounded">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Initialize Bootstrap components
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-close alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
    
    @stack('scripts')
</body>
</html> 