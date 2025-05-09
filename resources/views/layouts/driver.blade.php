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
    <link href="{{ asset('css/driver.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 p-0 driver-sidebar d-md-block" id="driverSidebar">
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
                    
                    @include('layouts.partials.driver-navigation')
                    
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
            <div class="col-md-9 col-lg-10 content-wrapper">
                <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 shadow-sm rounded">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" id="sidebarToggle">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <span class="navbar-brand">
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
                
                @include('layouts.partials.flash-messages')
                
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
    <script src="{{ asset('js/driver.js') }}"></script>
    
    @stack('scripts')
</body>
</html> 