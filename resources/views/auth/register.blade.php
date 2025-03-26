<x-guest-layout>
    <div class="auth-container">
        <div class="auth-header text-center mb-4">
            <h2 class="text-2xl font-bold text-primary">Create Account</h2>
            <p class="text-muted mt-1">Join Karen Culture Sales and discover authentic products</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="auth-form">
            @csrf

            <!-- Name -->
            <div class="form-group mb-4">
                <label for="name" class="form-label block mb-1">Full Name</label>
                <div class="input-group">
                    <span class="input-icon">
                        <i class="fas fa-user"></i>
                    </span>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" 
                        required autofocus autocomplete="name"
                        class="form-control @error('name') is-invalid @enderror" 
                        placeholder="Enter your full name">
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="form-group mb-4">
                <label for="email" class="form-label block mb-1">Email Address</label>
                <div class="input-group">
                    <span class="input-icon">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" 
                        required autocomplete="username"
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
                        autocomplete="new-password"
                        class="form-control @error('password') is-invalid @enderror" 
                        placeholder="Create a password">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                <small class="form-text text-muted">
                    Password must:
                    <ul class="mt-1 ps-3">
                        <li>Be at least 10 characters long</li>
                        <li>Include uppercase and lowercase letters</li>
                        <li>Include at least one number</li>
                        <li>Include at least one symbol</li>
                        <li>Not be a commonly used password</li>
                    </ul>
                </small>
            </div>

            <!-- Confirm Password -->
            <div class="form-group mb-4">
                <label for="password_confirmation" class="form-label block mb-1">Confirm Password</label>
                <div class="input-group">
                    <span class="input-icon">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input id="password_confirmation" type="password" name="password_confirmation" required 
                        autocomplete="new-password"
                        class="form-control" 
                        placeholder="Confirm your password">
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-karen w-100 py-2">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create Account
                </button>
            </div>

            <div class="auth-footer mt-4 text-center">
                <p class="mb-2">By registering, you agree to our <a href="{{ route('privacy') }}" class="text-primary">Privacy Policy</a></p>
                <p>Already have an account? <a href="{{ route('login') }}" class="text-primary">Sign In</a></p>
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
        
        .form-text {
            font-size: 0.75rem;
            color: var(--text-light);
            margin-top: 0.25rem;
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
