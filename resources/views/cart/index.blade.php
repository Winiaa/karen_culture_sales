@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Shopping Cart</h1>

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
                                @if($item->product->quantity <= 0)
                                    <span class="badge bg-danger mt-2">Out of Stock</span>
                                    <div class="alert alert-warning mt-2 mb-0 p-2 small">
                                        This product is currently out of stock. Please remove it from your cart to proceed with checkout.
                                    </div>
                                @endif
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
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" id="update-form-{{ $item->id }}">
                                    @csrf
                                    @method('PUT')
                                    <label for="quantity-{{ $item->id }}" class="form-label small">Quantity:</label>
                                    <div class="input-group">
                                        <input type="number" id="quantity-{{ $item->id }}" name="quantity" class="form-control cart-quantity" 
                                               value="{{ $item->quantity }}" min="1" max="{{ $item->product->quantity }}" @if($item->product->quantity <= 0) disabled @endif>
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Update quantity" @if($item->product->quantity <= 0) disabled @endif>
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-2 text-end">
                                <div class="mb-2 fw-bold">
                                    @baht($item->quantity * ($item->product->discount_price ?? $item->product->price))
                                </div>
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST">
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
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>@baht($subtotal)</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping</span>
                        <span>
                            @if($shippingCost > 0)
                                @baht($shippingCost)
                                <small class="text-muted d-block">
                                    Add @baht(config('shipping.free_shipping_threshold') - $subtotal) more for free shipping
                                </small>
                            @else
                                <span class="text-success">Free</span>
                            @endif
                        </span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total</strong>
                        <strong>@baht($total)</strong>
                    </div>
                    <div class="d-grid">
                        @auth
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
                                <button type="submit" class="btn btn-karen w-100">
                                    Proceed to Checkout
                                </button>
                            </form>
                        @else
                            <div class="alert alert-info">
                                <h5 class="alert-heading">Almost there!</h5>
                                <p>To complete your purchase, please login or create an account.</p>
                                <hr>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('login') }}" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login
                                    </a>
                                    <a href="{{ route('register') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-user-plus me-2"></i>Create Account
                                    </a>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>
@endsection 