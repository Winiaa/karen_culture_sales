<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="text-primary mb-3">Forgot Your Password?</h4>
        <p class="text-muted mb-4">
            No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
        </p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-envelope"></i>
                </span>
                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your email address">
            </div>
            @error('email')
                <div class="text-danger mt-2">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="d-grid gap-2">
            <x-primary-button>
                <i class="fas fa-paper-plane me-2"></i>
                {{ __('Send Reset Link') }}
            </x-primary-button>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Login
            </a>
        </div>
    </form>
</x-guest-layout>
