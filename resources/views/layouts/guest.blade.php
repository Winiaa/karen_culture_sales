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
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
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
                background-color: var(--bg-light);
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            
            .auth-page-container {
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
                padding: 2rem 1rem;
            }
            
            .auth-card {
                background-color: var(--white);
                border-radius: 10px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.05);
                padding: 2rem;
                max-width: 500px;
                width: 100%;
                margin: 0 auto;
            }
            
            .auth-logo {
                text-align: center;
                margin-bottom: 1.5rem;
            }
            
            .auth-logo img {
                height: 60px;
                width: auto;
            }
            
            .auth-footer-links {
                display: flex;
                justify-content: center;
                gap: 1.5rem;
                margin-top: 2rem;
                font-size: 0.875rem;
            }
            
            .auth-footer-links a {
                color: var(--text-light);
                text-decoration: none;
                transition: color 0.3s ease;
            }
            
            .auth-footer-links a:hover {
                color: var(--primary-color);
            }
            
            @media (min-width: 768px) {
                .auth-page-container {
                    padding: 3rem;
                }
                
                .auth-card {
                    padding: 2.5rem;
                }
            }
            
            .brand-logo {
                display: inline-block;
                text-decoration: none;
            }
            
            .brand-name {
                font-size: 1.5rem;
                font-weight: 700;
                color: var(--primary-color);
                letter-spacing: -0.5px;
                display: block;
                text-align: center;
                border-bottom: 2px solid var(--accent-color);
                padding-bottom: 0.5rem;
                transition: color 0.3s ease;
            }
            
            .brand-logo:hover .brand-name {
                color: var(--primary-light);
            }

            /* Modern button styles */
            .custom-btn {
                width: 100%;
                padding: 8px 16px;
                background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
                border: none;
                border-radius: 6px;
                color: white;
                font-weight: 600;
                font-size: 0.875rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(26, 71, 42, 0.2);
                position: relative;
                overflow: hidden;
            }

            .custom-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(26, 71, 42, 0.3);
                background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
            }

            .custom-btn:active {
                transform: translateY(1px);
                box-shadow: 0 2px 10px rgba(26, 71, 42, 0.2);
            }

            .custom-btn::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
                transform: translateX(-100%);
                transition: transform 0.6s ease;
            }

            .custom-btn:hover::after {
                transform: translateX(100%);
            }

            .custom-btn:focus {
                outline: none;
                box-shadow: 0 0 0 3px rgba(156, 170, 100, 0.3);
            }
        </style>
    </head>
    <body>
        <div class="auth-page-container">
            <div class="auth-card">
                <div class="auth-logo">
                    <a href="{{ route('home') }}" class="brand-logo">
                        <span class="brand-name">Karen Culture Sales</span>
                    </a>
                </div>
                
                {{ $slot }}
            </div>
            
            <div class="auth-footer-links">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('about') }}">About Us</a>
                <a href="{{ route('contact') }}">Contact</a>
                <a href="{{ route('privacy') }}">Privacy Policy</a>
            </div>
        </div>
        
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
