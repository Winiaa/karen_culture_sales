@extends('layouts.app')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">My Orders</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.show', $order) }}">Order #{{ $order->id }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Payment</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-7 mb-4">
            <div class="card">
                <div class="card-header bg-karen text-white">
                    <h5 class="mb-0">Secure Payment</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif

                    <form id="payment-form">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="card-element" class="form-label">Credit Card</label>
                                <div class="form-control card-field-container" id="card-element">
                                    <!-- Stripe Card Element will be inserted here -->
                                </div>
                                <div id="card-errors" class="text-danger mt-1" role="alert"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="card-expiry-element" class="form-label">Expiration Date</label>
                                <div class="form-control card-field-container" id="card-expiry-element">
                                    <!-- Stripe Expiry Element will be inserted here -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="card-cvc-element" class="form-label">CVC</label>
                                <div class="form-control card-field-container" id="card-cvc-element">
                                    <!-- Stripe CVC Element will be inserted here -->
                                </div>
                                <div class="cvc-helper mt-1">
                                    <i class="fas fa-question-circle"></i>
                                    3 or 4 digits on back of card
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-karen btn-lg shadow-sm" id="submit-button" disabled>
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-lock me-2"></i>
                                    <span id="button-text">Pay @baht($order->total_amount)</span>
                                    <div class="spinner d-none" id="spinner"></div>
                                </div>
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 text-center">
                        <div class="security-provider mb-2">
                            <div class="stripe-badge">
                                <i class="fab fa-stripe stripe-icon"></i>
                                <span class="stripe-text">Powered by Stripe</span>
                            </div>
                        </div>
                        <div class="payment-methods">
                            <img src="https://js.stripe.com/v3/fingerprinted/img/visa-365725566f9578a9589553aa9296d178.svg" 
                                alt="Visa" width="40" height="25">
                            <img src="https://js.stripe.com/v3/fingerprinted/img/mastercard-4d8844094130711885b5e41b28c9848f.svg" 
                                alt="Mastercard" width="40" height="25">
                            <img src="https://js.stripe.com/v3/fingerprinted/img/amex-a49b82f46c5cd6a96a6e418a6ca1717c.svg" 
                                alt="American Express" width="40" height="25">
                            <img src="https://js.stripe.com/v3/fingerprinted/img/discover-ac52cd46f89fa40a29a0bfb954e33173.svg" 
                                alt="Discover" width="40" height="25">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Order #:</strong> {{ $order->id }}
                    </p>

                    <div class="mb-3">
                        <strong>Items:</strong>
                        <div class="mt-2">
                            @foreach($order->orderItems as $item)
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->image }}" 
                                     class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                <div class="ms-3">
                                    <div>{{ $item->product->title }}</div>
                                    <small class="text-muted">
                                        {{ $item->quantity }} × @baht($item->product->final_price)
                                    </small>
                                </div>
                                <div class="ms-auto">
                                    @baht($item->subtotal)
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>@baht($order->total_amount)</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span>@baht($order->total_amount)</span>
                    </div>
                </div>
            </div>

            <div class="small text-muted">
                <p>By proceeding with this payment, you agree to our <a href="{{ route('privacy') }}">Terms of Service and Privacy Policy</a>.</p>
                <p>Your payment information is securely processed by Stripe. We do not store your card details on our servers.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
:root {
    --primary-color: #1a472a; /* Dark green - main brand color */
    --primary-light: #2d634c; /* Lighter green for hover states */
    --primary-dark: #0e3019; /* Darker green for active states */
    --accent-color: #9caa64; /* Sage green accent */
    --accent-light: #d4ddb9; /* Light sage for hover states */
}

.bg-karen {
    background-color: var(--primary-color);
    color: white;
}

.btn-karen {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.btn-karen:hover:not(:disabled) {
    background-color: var(--primary-light);
    border-color: var(--primary-light);
    color: white;
}

.btn-karen:disabled {
    background-color: #bbb;
    border-color: #bbb;
    cursor: not-allowed;
}

.spinner {
    width: 20px;
    height: 20px;
    margin-left: 10px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.card-field-container {
    min-height: 40px;
    padding: 10px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    background-color: #fff;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.card-field-container.StripeElement--focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(26, 71, 42, 0.15);
}

.card-field-container.StripeElement--invalid {
    border-color: #dc3545;
}

.form-text {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #6c757d;
}

.cvc-helper {
    display: flex;
    align-items: center;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #6c757d;
}

.cvc-helper i {
    margin-right: 0.25rem;
    color: var(--primary-color);
}

/* Responsive adjustments */
@media (max-width: 767.98px) {
    .card-expiry-element, .card-cvc-element {
        margin-top: 1rem;
    }
}

.security-provider {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    background-color: white;
    display: inline-block;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 1rem !important;
}

.stripe-badge {
    display: flex;
    align-items: center;
    justify-content: center;
}

.stripe-icon {
    font-size: 2rem;
    color: #635BFF;
    margin-right: 8px;
}

.stripe-text {
    font-weight: 600;
    color: #32325d;
    font-size: 0.9rem;
}

.payment-methods {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-top: 0.5rem;
}

.card-brand {
    position: relative;
    width: 40px;
    height: 25px;
}

.payment-methods img {
    display: block;
    height: 25px;
    width: auto;
    transition: transform 0.2s;
    filter: none;
    opacity: 1;
    position: relative;
    z-index: 2;
}

.fallback-icon {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 1;
    opacity: 0.8;
    color: var(--primary-color);
}

.payment-methods img:hover {
    transform: translateY(-2px);
}
</style>
@endpush

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add debug info to the page
        const debugContainer = document.createElement('div');
        debugContainer.className = 'alert alert-warning mb-4 d-none';
        debugContainer.id = 'debug-info';
        document.querySelector('.card-body').insertBefore(debugContainer, document.querySelector('#payment-form'));
        
        function showDebug(message) {
            const debugInfo = document.getElementById('debug-info');
            debugInfo.classList.remove('d-none');
            debugInfo.innerHTML = '<strong>Debug:</strong> ' + message;
        }
        
        // Get the CSRF token directly from the meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        if (!csrfToken) {
            showDebug('CSRF token not found. This will cause payment failures. Please refresh the page.');
        }
        
        // Initialize Stripe
        let stripe;
        try {
            stripe = Stripe('{{ $stripeKey }}');
            console.log('Stripe initialized successfully');
        } catch (error) {
            console.error('Failed to initialize Stripe:', error);
            showDebug('Failed to initialize Stripe: ' + error.message);
            document.getElementById('submit-button').disabled = true;
            return;
        }
        
        const elements = stripe.elements();
        
        // Custom styling for the card elements
        const style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                },
                ':focus': {
                    color: '#1a472a'
                },
                iconColor: '#1a472a'
            },
            invalid: {
                color: '#dc3545',
                iconColor: '#dc3545'
            }
        };
        
        // Create individual card elements
        const cardNumberElement = elements.create('cardNumber', {
            style: style,
            placeholder: 'Card number',
            showIcon: true
        });
        cardNumberElement.mount('#card-element');
        
        const cardExpiryElement = elements.create('cardExpiry', { 
            style: style,
            placeholder: 'MM / YY'
        });
        cardExpiryElement.mount('#card-expiry-element');
        
        const cardCvcElement = elements.create('cardCvc', { 
            style: style,
            placeholder: 'CVC'
        });
        cardCvcElement.mount('#card-cvc-element');
        
        // Handle validation errors
        const displayError = document.getElementById('card-errors');
        const submitButton = document.getElementById('submit-button');
        
        // Enable the submit button once elements are ready
        submitButton.disabled = false;
        
        [cardNumberElement, cardExpiryElement, cardCvcElement].forEach(element => {
            element.addEventListener('change', function(event) {
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });
        });
        
        // Handle form submission
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            setLoading(true);
            displayError.textContent = '';
            
            try {
                const result = await stripe.createPaymentMethod({
                    type: 'card',
                    card: cardNumberElement,
                    billing_details: {
                        email: '{{ auth()->user()->email }}'
                    }
                });
                
                if (result.error) {
                    // Show error to your customer
                    displayError.textContent = result.error.message;
                    console.error('Stripe createPaymentMethod error:', result.error);
                    setLoading(false);
                } else {
                    // Send the payment method ID to your server
                    await stripePaymentMethodHandler(result.paymentMethod.id);
                }
            } catch (error) {
                console.error('Error in payment submission:', error);
                displayError.textContent = 'An unexpected error occurred. Please try again. ' + error.message;
                setLoading(false);
            }
        });
        
        // Function to handle the payment
        async function stripePaymentMethodHandler(paymentMethodId) {
            try {
                console.log('Processing payment with method ID:', paymentMethodId);
                console.log('Using CSRF token:', csrfToken);
                
                const fetchOptions = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        payment_method_id: paymentMethodId,
                        return_url: window.location.origin + '{{ route("payments.callback", [], false) }}'
                    })
                };
                
                console.log('Sending request to:', '{{ route("payments.process", $order) }}');
                console.log('Request options:', fetchOptions);
                
                const response = await fetch('{{ route("payments.process", $order) }}', fetchOptions);
                
                console.log('Response status:', response.status);
                console.log('Response headers:', Object.fromEntries([...response.headers]));
                
                let data;
                const contentType = response.headers.get('Content-Type');
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                    console.log('Server response (JSON):', data);
                } else {
                    const text = await response.text();
                    console.log('Server response (text):', text);
                    try {
                        // Sometimes a JSON response doesn't have the correct content type
                        data = JSON.parse(text);
                    } catch (e) {
                        throw new Error(`Server responded with status ${response.status}: ${text}`);
                    }
                }
                
                if (!response.ok && !data) {
                    throw new Error(`Server responded with status ${response.status}`);
                }
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                if (data.requires_action) {
                    // Use Stripe.js to handle required card action
                    console.log('Additional action required. Confirming card payment...');
                    const result = await stripe.confirmCardPayment(data.payment_intent_client_secret, {
                        return_url: window.location.origin + '{{ route("payments.callback", [], false) }}'
                    });
                    
                    if (result.error) {
                        // Show error
                        console.error('confirmCardPayment error:', result.error);
                        throw new Error(result.error.message);
                    } else {
                        console.log('Card payment confirmed, sending confirmation to server...');
                        // Action handled, submit the confirmation to your server
                        const confirmResponse = await fetch('{{ route("payments.confirm", $order) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                payment_intent_id: result.paymentIntent.id
                            })
                        });
                        
                        if (!confirmResponse.ok) {
                            const confirmText = await confirmResponse.text();
                            console.error('Confirmation response:', confirmText);
                            throw new Error(`Confirmation failed with status ${confirmResponse.status}: ${confirmText}`);
                        }
                        
                        const confirmData = await confirmResponse.json();
                        console.log('Confirmation response:', confirmData);
                        
                        if (confirmData.error) {
                            throw new Error(confirmData.error);
                        }
                        
                        // Redirect to the order page
                        window.location.href = confirmData.redirect || '{{ route("orders.show", $order) }}?payment_success=true';
                    }
                } else if (data.success) {
                    // No additional action required, redirect to success page
                    window.location.href = data.redirect || '{{ route("orders.show", $order) }}?payment_success=true';
                } else {
                    throw new Error('Invalid response from server. Please try again.');
                }
            } catch (error) {
                console.error('Payment processing error:', error);
                displayError.textContent = 'Payment failed. Please try again. ' + 
                    (error.message || 'Error communicating with the payment server.');
                setLoading(false);
                showDebug('Error: ' + error.message);
            }
        }
        
        // Helper function to show/hide loading state
        function setLoading(isLoading) {
            if (isLoading) {
                document.getElementById('submit-button').disabled = true;
                document.getElementById('button-text').classList.add('opacity-0');
                document.getElementById('spinner').classList.remove('d-none');
            } else {
                document.getElementById('submit-button').disabled = false;
                document.getElementById('button-text').classList.remove('opacity-0');
                document.getElementById('spinner').classList.add('d-none');
            }
        }
        
        // Add a small helper to display test card information
        document.querySelector('.card-body').insertAdjacentHTML('afterbegin', `
            <div class="alert alert-info mb-4" role="alert">
                <strong>Test Mode:</strong> Use these test cards:
                <ul class="mb-0 mt-1">
                    <li>Card: 4242 4242 4242 4242 (Success)</li>
                    <li>Expiry: Any future date (MM/YY)</li>
                    <li>CVC: Any 3 digits</li>
                </ul>
            </div>
        `);
    });
</script>
@endpush 