<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') | Karen Culture Sales</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --sidebar-width: 260px;
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
            --success-color: #28a745;
            --info-color: #17a2b8;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            background-color: var(--bg-light);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        
        /* Admin layout */
        .admin-container {
            display: flex;
            flex: 1;
        }
        
        .admin-content {
            flex: 1;
            min-height: 100vh;
            padding: 20px;
            transition: all 0.3s;
            margin-left: var(--sidebar-width);
        }
        
        .admin-sidebar {
            width: var(--sidebar-width);
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            background-color: var(--primary-color);
            color: var(--white);
            z-index: 1000;
            transition: all 0.3s;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-header {
            padding: 20px;
            background-color: var(--primary-dark);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .sidebar-brand {
            color: var(--white);
            font-weight: 700;
            font-size: 1.2rem;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .sidebar-brand i {
            margin-right: 10px;
        }
        
        .sidebar-user {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 15px;
            background-color: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }
        
        .user-name {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .user-role {
            font-size: 0.85rem;
            opacity: 0.7;
        }
        
        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding: 20px 0;
        }
        
        .menu-header {
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
            font-weight: 600;
            color: var(--accent-light);
            padding: 10px 20px;
            margin-top: 10px;
        }
        
        .nav-item {
            margin: 5px 15px;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 10px;
            display: flex;
            align-items: center;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: var(--white);
        }
        
        .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        
        .sidebar-footer {
            padding: 15px 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .footer-link {
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
            text-decoration: none;
        }
        
        .footer-link:hover {
            color: var(--white);
        }
        
        .content-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background-color: var(--white);
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .topbar-title h1 {
            margin-bottom: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .topbar-title p {
            margin-bottom: 0;
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .topbar-actions .btn {
            margin-left: 10px;
        }
        
        /* Mobile adjustments */
        @media (max-width: 991.98px) {
            .admin-sidebar {
                left: calc(-1 * var(--sidebar-width));
            }
            
            .admin-content {
                margin-left: 0;
                width: 100%;
            }
            
            .sidebar-open .admin-sidebar {
                left: 0;
            }
            
            .sidebar-open .admin-content {
                transform: translateX(var(--sidebar-width));
            }
            
            .content-topbar {
                position: sticky;
                top: 0;
                z-index: 999;
            }
        }
        
        /* Utility classes */
        .bg-primary {
            background-color: var(--primary-color) !important;
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
        
        .btn-menu {
            background: none;
            border: none;
            color: var(--primary-color);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
            display: none;
        }
        
        @media (max-width: 991.98px) {
            .btn-menu {
                display: block;
            }
        }
        
        .admin-sidebar .nav-link {
            border-radius: 4px;
            margin: 2px 0;
            transition: all 0.2s;
        }
        
        .admin-sidebar .nav-link:hover {
            background-color: var(--primary-light);
        }
        
        .admin-sidebar .nav-link.active {
            background-color: var(--accent-color) !important;
            color: var(--white) !important;
        }
        
        /* Admin user info styles */
        .admin-user-info {
            padding: 1rem 0;
        }
        
        .admin-avatar {
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }
        
        .admin-name {
            font-weight: 500;
            margin-bottom: 0.2rem;
        }
        
        .admin-role {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .content-topbar {
            background-color: var(--white);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            border-radius: 8px;
        }
        
        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-weight: 500;
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: var(--white);
        }
        
        /* Karen Culture Custom Button */
        .btn-karen {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .btn-karen:hover {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
            color: white;
        }
        
        /* Dashboard cards hover effect */
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        /* Custom table styling */
        .table th {
            background-color: var(--bg-medium);
            border-color: var(--bg-light);
        }
        
        /* Custom pagination styling */
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .pagination .page-link {
            color: var(--primary-color);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="admin-container" id="adminContainer">
        <div class="admin-sidebar">
            <div class="sidebar-header p-3 border-bottom border-dark">
                <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none fs-4">
                    <i class="fas fa-leaf me-2"></i> Karen Culture
                </a>
                <button class="btn btn-sm btn-outline-light float-end d-lg-none" id="sidebarClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="sidebar-menu p-3">
                <div class="admin-user-info mb-4 text-center text-white">
                    <div class="admin-avatar mb-2">
                        <img src="{{ Auth::user()->profile_picture_url }}" alt="{{ Auth::user()->name }}" class="img-fluid rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                    </div>
                    <div class="admin-name">{{ Auth::user()->name }}</div>
                    <div class="admin-role text-white-50">Administrator</div>
                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-sm btn-outline-light mt-2">
                        <i class="fas fa-user-cog me-1"></i> My Profile
                    </a>
                </div>
                
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.products.index') }}" class="nav-link text-white {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                            <i class="fas fa-box me-2"></i> Products
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.categories.index') }}" class="nav-link text-white {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="fas fa-tags me-2"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.orders.index') }}" class="nav-link text-white {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart me-2"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.drivers.index') }}" class="nav-link text-white {{ request()->routeIs('admin.drivers.*') ? 'active' : '' }}">
                            <i class="fas fa-truck me-2"></i> Drivers
                        </a>
                    </li>
                    <!-- Reports section -->
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.reports.sales') }}" class="nav-link text-white {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar me-2"></i> Sales Report
                        </a>
                    </li>
                    <!-- Users management -->
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.users.index') }}" class="nav-link text-white {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="fas fa-users me-2"></i> Users
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="mt-auto p-3 border-top border-dark">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
        
        <div class="admin-content">
            <div class="content-topbar shadow-sm mb-4 rounded">
                <button class="btn btn-primary d-lg-none me-2" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="mb-0 d-inline-block">@yield('title')</h4>
                <p class="text-muted mb-0">@yield('subtitle')</p>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const adminContainer = document.getElementById('adminContainer');
            
            sidebarToggle.addEventListener('click', function() {
                adminContainer.classList.toggle('sidebar-open');
            });
            
            sidebarClose.addEventListener('click', function() {
                adminContainer.classList.remove('sidebar-open');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (adminContainer.classList.contains('sidebar-open') && 
                    !event.target.closest('.admin-sidebar') && 
                    !event.target.closest('#sidebarToggle')) {
                    adminContainer.classList.remove('sidebar-open');
                }
            });
        });
    </script>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html> 