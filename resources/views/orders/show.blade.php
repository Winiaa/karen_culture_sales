@extends('layouts.app')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">My Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">Order #{{ $order->id }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <!-- Order Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Status</h5>
                </div>
                <div class="card-body">
                    <x-customer.order-timeline :order="$order" />
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    @foreach($order->orderItems as $item)
                    <div class="d-flex mb-3 pb-3 border-bottom">
                        <img src="{{ Storage::url($item->product->image) }}" class="rounded" alt="{{ $item->product->title }}" style="width: 100px; height: 100px; object-fit: cover;">
                        <div class="ms-3 flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">{{ $item->product->title }}</h6>
                                    <p class="mb-1 text-muted">{{ $item->product->category->category_name }}</p>
                                    <p class="mb-0">Quantity: {{ $item->quantity }}</p>
                                </div>
                                <div class="text-end">
                                    <p class="mb-1">@baht($item->product->final_price) each</p>
                                    <p class="mb-0"><strong>Subtotal: @baht($item->subtotal)</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>@baht($order->subtotal)</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping</span>
                        <span>
                            @if($order->subtotal >= config('shipping.free_shipping_threshold'))
                                <span class="text-success">Free</span>
                            @else
                                @baht($order->shipping_cost)
                            @endif
                        </span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total</strong>
                        <strong>@baht($order->total_amount)</strong>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Payment Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <div class="small text-muted">Payment Method</div>
                                    <div>
                                        @if($order->payment)
                                            {{ ucfirst(str_replace('_', ' ', $order->payment->payment_method)) }}
                                            @if($order->payment->payment_method === 'cash_on_delivery')
                                                <small class="d-block text-muted mt-1">Payment collected upon delivery</small>
                                            @endif
                                        @else
                                            Not specified
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <div class="small text-muted">Payment Status</div>
                                    <div>
                                        @if($order->payment)
                                            @if($order->order_status === 'cancelled')
                                                @if($order->payment->payment_method === 'stripe')
                                                    <span class="text-info"><i class="fas fa-undo me-1"></i> Refunded</span>
                                                @else
                                                    <span class="text-danger"><i class="fas fa-times-circle me-1"></i> Cancelled</span>
                                                @endif
                                            @elseif($order->payment->payment_status === 'completed')
                                                <span class="text-success"><i class="fas fa-check-circle me-1"></i> Paid</span>
                                            @elseif($order->payment->payment_status === 'pending')
                                                @if($order->payment->payment_method === 'cash_on_delivery')
                                                    @if($order->delivery && $order->delivery->delivery_status === 'delivered')
                                                        <span class="text-success"><i class="fas fa-check-circle me-1"></i> Paid on delivery</span>
                                                    @else
                                                        <span class="text-warning"><i class="fas fa-clock me-1"></i> To be paid on delivery</span>
                                                    @endif
                                                @else
                                                    <span class="text-warning"><i class="fas fa-clock me-1"></i> Pending</span>
                                                @endif
                                            @else
                                                <span class="text-danger"><i class="fas fa-times-circle me-1"></i> Failed</span>
                                            @endif
                                        @else
                                            Not specified
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($order->payment && $order->payment->transaction_id)
                    <div class="bg-light p-3 rounded">
                        <div class="small text-muted">Transaction ID</div>
                        <div class="font-monospace">{{ $order->payment->transaction_id }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Delivery Information -->
            @if($order->delivery && $order->order_status !== 'cancelled')
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Delivery Information</h5>
                    @if($order->delivery->delivery_status === 'delivered')
                    <span class="badge bg-success">Delivered</span>
                    @elseif($order->delivery->delivery_status === 'out_for_delivery')
                    <span class="badge bg-info">Out for Delivery</span>
                    @elseif($order->delivery->delivery_status === 'picked_up')
                    <span class="badge bg-primary">Picked Up</span>
                    @elseif($order->delivery->delivery_status === 'assigned')
                    <span class="badge bg-secondary">Driver Assigned</span>
                    @elseif($order->delivery->delivery_status === 'failed')
                    <span class="badge bg-danger">Delivery Failed</span>
                    @else
                    <span class="badge bg-warning">Processing</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($order->delivery->driver)
                    <div class="mb-4 p-3 bg-light rounded border-start border-primary border-3">
                        <h6 class="mb-2"><i class="fas fa-truck me-2"></i>Your Delivery Driver</h6>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">{{ $order->delivery->driver->user->name }}</h6>
                                <span class="badge bg-info">{{ ucfirst($order->delivery->driver->vehicle_type) }} Driver</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <strong>Recipient:</strong> {{ $order->delivery->recipient_name }}
                    </div>
                    <div class="mb-3">
                        <strong>Phone:</strong> {{ $order->delivery->recipient_phone }}
                    </div>
                    <div class="mb-3">
                        <strong>Delivery Address:</strong><br>
                        {{ $order->delivery->recipient_address }}
                    </div>
                    
                    @if($order->delivery->estimated_delivery_date)
                    <div class="mb-3">
                        <strong>Estimated Delivery:</strong><br>
                        {{ $order->delivery->estimated_delivery_date->format('M d, Y') }}
                    </div>
                    @endif

                    <div class="mb-3">
                        <strong>Tracking Number:</strong><br>
                        <span class="tracking-number">{{ $order->delivery->tracking_number }}</span>
                        <div class="mt-3">
                            <a href="{{ route('tracking.index') }}?tracking_number={{ $order->delivery->tracking_number }}" class="btn btn-primary btn-block w-100">
                                <i class="fas fa-search me-2"></i> Track Your Package
                            </a>
                        </div>
                    </div>
                    
                    @if($order->delivery->delivered_at)
                    <div class="mb-3">
                        <strong>Delivery Confirmation:</strong><br>
                        <div class="text-success">
                            <i class="fas fa-check-circle me-1"></i> Delivered on {{ $order->delivery->delivered_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @elseif($order->order_status === 'cancelled')
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Delivery Information</h5>
                    <span class="badge bg-danger">Cancelled</span>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">This order has been cancelled. No delivery information is available.</p>
                </div>
            </div>
            @else
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Delivery Information</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">Delivery details will appear here once your order is processed.</p>
                </div>
            </div>
            @endif

            <!-- Actions -->
            @if($order->canBeCancelled())
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Actions</h5>
                </div>
                <div class="card-body">
                    @if($order->payment && $order->payment->payment_method === 'stripe' && $order->paid_at)
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            You can cancel this order within 20 minutes of payment.
                            @if($order->getRemainingCancellationTime() > 0)
                                <strong>{{ $order->getRemainingCancellationTime() }} minutes remaining.</strong>
                            @endif
                        </div>
                    @endif
                    
                    <form action="{{ route('orders.cancel', $order) }}" method="POST" id="cancelOrderForm">
                        @csrf
                        <div class="d-grid">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                                <i class="fas fa-times-circle me-2"></i>Cancel Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @elseif($order->order_status === 'processing')
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-3">
                            @if($order->payment && $order->payment->payment_method === 'stripe' && $order->paid_at && $order->paid_at->diffInMinutes(now()) >= 20)
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                This order cannot be cancelled because the 20-minute cancellation window has expired.
                            @elseif($order->payment && $order->payment->payment_method === 'cash_on_delivery' && $order->delivery && $order->delivery->isOutForDelivery())
                                <i class="fas fa-truck me-2"></i>
                                This order is out for delivery and cannot be cancelled.
                            @else
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                This order cannot be cancelled.
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="cancelOrderModalLabel">Cancel Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                </div>
                <p class="text-center mb-0">Are you sure you want to cancel this order? This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep Order</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('cancelOrderForm').submit();">
                    <i class="fas fa-times-circle me-2"></i>Yes, Cancel Order
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.status-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin: 0 auto;
}

.status-line {
    flex: 1;
    height: 2px;
    background: #e9ecef;
    margin: 25px 10px 0;
    position: relative;
}

/* Status line colors based on order status */
.text-center:nth-child(1) .status-circle.bg-success ~ .status-line {
    background: #28a745;
}

.text-center:nth-child(1) .status-circle.bg-danger ~ .status-line {
    background: #dc3545;
}

.text-center:nth-child(3) .status-circle.bg-success ~ .status-line {
    background: #28a745;
}

.text-center:nth-child(3) .status-circle.bg-danger ~ .status-line {
    background: #dc3545;
}

.text-center:nth-child(3) .status-circle.bg-info ~ .status-line {
    background: #0dcaf0;
}

.text-center:nth-child(3) .status-circle.bg-secondary ~ .status-line {
    background: #e9ecef;
}

.text-center:nth-child(5) .status-circle.bg-success ~ .status-line {
    background: #28a745;
}

.text-center:nth-child(5) .status-circle.bg-secondary ~ .status-line {
    background: #e9ecef;
}

/* Fix for the last status line */
.text-center:last-child .status-line {
    display: none;
}

.bg-secondary {
    background-color: #6c757d !important;
}

.bg-karen {
    background-color: #8D6E63;
    color: white;
}

/* Tracking Progress Styles */
.tracking-progress {
    display: flex;
    flex-direction: column;
}

.step {
    display: flex;
    position: relative;
    z-index: 1;
}

.step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 1rem;
    margin-right: 15px;
}

.step.completed .step-icon {
    background-color: #8D6E63;
    color: white;
}

.step-content {
    padding: 5px 0;
}

.step-line {
    width: 3px;
    height: 25px;
    background-color: #e9ecef;
    margin-left: 18px;
    z-index: 0;
}

.step.completed + .step-line {
    background-color: #8D6E63;
}

.step-content h6 {
    margin-bottom: 0;
    font-size: 0.9rem;
}

.step-content p {
    font-size: 0.75rem;
    margin-bottom: 0;
}

.tracking-number {
    font-family: monospace;
    font-size: 1.1rem;
    font-weight: 500;
}

.tracking-info {
    border-left: 4px solid #8D6E63;
}

.address-card {
    border-left: 4px solid #8D6E63;
}

.product-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
}

.btn-karen {
    background-color: #8D6E63;
    border-color: #8D6E63;
    color: white;
}

.btn-karen:hover {
    background-color: #795548;
    border-color: #795548;
    color: white;
}
</style>
@endsection 