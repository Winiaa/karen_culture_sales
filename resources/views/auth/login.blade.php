<x-guest-layout>
    <div class="auth-container">
        <div class="auth-header text-center mb-4">
            <h2 class="text-2xl font-bold text-primary">Welcome Back</h2>
            <p class="text-muted mt-1">Sign in to your Karen Culture Sales account</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />
        
        @if(isset($isSwitchingUser) && $isSwitchingUser)
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Admin Switch Mode:</strong>
                <span class="block sm:inline"> You are about to login as a different user. Your admin session will end.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf
            
            @if(request()->has('switch_to_user'))
                <input type="hidden" name="switch_to_user" value="1">
            @endif

            <!-- Email Address -->
            <div class="form-group mb-4">
                <label for="email" class="form-label block mb-1">Email Address</label>
                <div class="input-group">
                    <span class="input-icon">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" 
                        required autofocus autocomplete="username"
                        class="form-control @error('email') is-invalid @enderror" 
                        placeholder="Enter your email">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="form-group mb-4">
                <label for="password" class="form-label block mb-1">Password</label>
                <div class="input-group">
                    <span class="input-icon">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input id="password" type="password" name="password" required 
                        autocomplete="current-password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Enter your password">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="form-check mb-4">
                <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
                <label for="remember_me" class="form-check-label">
                    Remember me
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-karen w-100 py-2">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In
                </button>
            </div>

            <div class="auth-footer mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    @if (Route::has('password.request'))
                        <a class="text-sm text-primary" href="{{ route('password.request') }}">
                            <i class="fas fa-key mr-1"></i> Forgot your password?
                        </a>
                    @endif

                    <a class="text-sm text-primary" href="{{ route('register') }}">
                        <i class="fas fa-user-plus mr-1"></i> Create an Account
                    </a>
                </div>
            </div>
        </form>
    </div>

    <style>
        .auth-container {
            max-width: 450px;
            margin: 0 auto;
        }
        
        .auth-header h2 {
            color: var(--primary-color);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--text-color);
        }
        
        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .input-icon {
            position: absolute;
            left: 12px;
            color: var(--text-light);
            z-index: 10;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 10px 10px 40px;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(26, 71, 42, 0.2);
            outline: none;
        }
        
        .is-invalid {
            border-color: #ef4444 !important;
        }
        
        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-check-input {
            width: 1rem;
            height: 1rem;
        }
        
        .form-check-label {
            color: var(--text-light);
            font-size: 0.875rem;
        }

        .btn-karen {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: var(--white);
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-karen:hover {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
            transform: translateY(-1px);
        }
        
        .auth-footer {
            font-size: 0.875rem;
        }
        
        .auth-footer a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .auth-footer a:hover {
            color: var(--primary-light);
            text-decoration: underline;
        }
    </style>
</x-guest-layout>
