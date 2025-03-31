@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Shopping Cart</h1>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($cartItems->isEmpty())
    <div class="alert alert-info">
        Your cart is empty. <a href="{{ route('products.index') }}" class="alert-link">Continue shopping</a>
    </div>
    @else
    <div class="row">
        <!-- Cart Items -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @foreach($cartItems as $item)
                    <div class="cart-item mb-3 pb-3 border-bottom">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <img src="{{ Storage::url($item->product->image) }}" class="img-fluid rounded" alt="{{ $item->product->title }}">
                            </div>
                            <div class="col-md-4">
                                <h5 class="mb-1">
                                    <a href="{{ route('products.show', $item->product) }}" class="text-dark text-decoration-none">
                                        {{ $item->product->title }}
                                    </a>
                                </h5>
                                <p class="text-muted mb-0">{{ $item->product->category->category_name }}</p>
                            </div>
                            <div class="col-md-2">
                                <div class="price">
                                    @if($item->product->discount_price)
                                    <div>
                                        <span class="text-danger">@baht($item->product->discount_price)</span>
                                        <small class="text-muted text-decoration-line-through">@baht($item->product->price)</small>
                                    </div>
                                    @else
                                    <span>@baht($item->product->price)</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-2">
                                <form action="{{ route('cart.update', $item) }}" method="POST" id="update-form-{{ $item->id }}">
                                    @csrf
                                    @method('PUT')
                                    <label for="quantity-{{ $item->id }}" class="form-label small">Quantity:</label>
                                    <div class="input-group">
                                        <input type="number" id="quantity-{{ $item->id }}" name="quantity" class="form-control cart-quantity" 
                                               value="{{ $item->quantity }}" min="1" max="{{ $item->product->quantity }}">
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Update quantity">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-2 text-end">
                                <div class="mb-2 fw-bold">
                                    @baht($item->quantity * ($item->product->discount_price ?? $item->product->price))
                                </div>
                                <form action="{{ route('cart.remove', $item) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove item">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal</span>
                        <span>@baht($total)</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <strong>Total</strong>
                        <strong>@baht($total)</strong>
                    </div>

                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="recipient_name" class="form-label">Recipient Name</label>
                            <input type="text" class="form-control" id="recipient_name" name="recipient_name" value="{{ auth()->user()->default_recipient_name ?? old('recipient_name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="recipient_phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="recipient_phone" name="recipient_phone" value="{{ auth()->user()->default_recipient_phone ?? old('recipient_phone') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="recipient_address" class="form-label">Delivery Address</label>
                            <textarea class="form-control" id="recipient_address" name="recipient_address" rows="4" placeholder="Please write a detailed delivery address including:
- Street address
- Building name (if applicable)
- Floor/Unit number (if applicable)
- Landmarks (if any)
- City
- Postal code" required>{{ auth()->user()->default_shipping_address ?? old('recipient_address') }}</textarea>
                            <small class="text-muted">Please provide as much detail as possible to ensure accurate delivery.</small>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="save_shipping_info" name="save_shipping_info" value="1" {{ auth()->user()->save_shipping_info ? 'checked' : '' }}>
                            <label class="form-check-label" for="save_shipping_info">Save shipping information for future orders</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="stripe" value="stripe" checked>
                                <label class="form-check-label" for="stripe">
                                    <i class="fab fa-cc-stripe me-2"></i>Credit Card
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash_on_delivery">
                                <label class="form-check-label" for="cash">
                                    <i class="fas fa-money-bill-wave me-2"></i>Cash on Delivery
                                </label>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-karen">
                                Proceed to Checkout
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <div class="mt-4">
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i> Continue Shopping
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add confirmation for removing items
        const deleteForms = document.querySelectorAll('form[action^="{{ route('cart.remove', ['cart' => 0]) }}"]');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to remove this item from your cart?')) {
                    e.preventDefault();
                    return false;
                }
            });
        });
        
        // Prevent clicks on cart quantity inputs from navigating
        document.querySelectorAll('.cart-quantity').forEach(input => {
            input.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent click propagation
            });
            
            // Also add a change handler to auto-submit the form
            input.addEventListener('change', function() {
                this.closest('form').submit();
            });
        });
        
        // Capture and handle form submission errors
        document.querySelectorAll('form[id^="update-form-"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                // Ensure the form uses POST method with the correct _method override
                this.method = 'POST';
                
                // Make sure we have a _method input with value PUT
                let methodInput = this.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    this.appendChild(methodInput);
                }
                methodInput.value = 'PUT';
            });
        });
    });
</script>
@endpush 