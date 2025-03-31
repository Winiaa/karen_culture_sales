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

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('shipping_saved'))
    <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
        Your shipping information has been saved for future orders.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(request()->has('payment_success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        Your payment was successful!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Order Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-4">
                        <div class="text-center">
                            <div class="status-circle {{ $order->order_status != 'cancelled' ? 'bg-success' : 'bg-danger' }}">
                                <i class="fas fa-check"></i>
                            </div>
                            <p class="mt-2 mb-0">Order Placed</p>
                            <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                        </div>
                        <div class="status-line"></div>
                        <div class="text-center">
                            <div class="status-circle {{ $order->payment_status === 'completed' ? 'bg-success' : 'bg-secondary' }}">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <p class="mt-2 mb-0">Payment</p>
                            <small class="text-muted">{{ ucfirst($order->payment_status) }}</small>
                        </div>
                        <div class="status-line"></div>
                        <div class="text-center">
                            <div class="status-circle {{ $order->order_status === 'shipped' || $order->order_status === 'delivered' ? 'bg-success' : 'bg-secondary' }}">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                            <p class="mt-2 mb-0">Shipped</p>
                            @if($order->delivery && ($order->order_status === 'shipped' || $order->order_status === 'delivered'))
                            <small class="text-muted">{{ $order->delivery->updated_at->format('M d, Y') }}</small>
                            @endif
                        </div>
                        <div class="status-line"></div>
                        <div class="text-center">
                            <div class="status-circle {{ $order->order_status === 'delivered' ? 'bg-success' : 'bg-secondary' }}">
                                <i class="fas fa-home"></i>
                            </div>
                            <p class="mt-2 mb-0">Delivered</p>
                            @if($order->delivery && $order->delivery->delivery_status === 'delivered')
                            <small class="text-muted">{{ $order->delivery->delivered_at ? $order->delivery->delivered_at->format('M d, Y') : $order->delivery->updated_at->format('M d, Y') }}</small>
                            @endif
                        </div>
                    </div>
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
                        <span>@baht($order->total_amount)</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping</span>
                        <span>Free</span>
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
                        <h5 class="fs-6">Payment Information</h5>
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
                                            @if($order->payment->payment_status === 'completed')
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
                    <p class="mb-0">
                        <strong>Transaction ID:</strong><br>
                        {{ $order->payment->transaction_id }}
                    </p>
                    @endif
                </div>
            </div>

            <!-- Delivery Information -->
            @if($order->delivery)
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
                    
                    <div class="mt-4 pt-3 border-top">
                        <h6><i class="fas fa-shipping-fast me-2"></i>Tracking Information</h6>
                        <div class="mb-3">
                            <strong>Tracking Number:</strong><br>
                            <span class="tracking-number">{{ $order->delivery->tracking_number }}</span>
                            
                            <div class="mt-3">
                                <a href="{{ route('tracking.index') }}?tracking_number={{ $order->delivery->tracking_number }}" class="btn btn-primary btn-block w-100">
                                    <i class="fas fa-search me-2"></i> Track Your Package
                                </a>
                            </div>
                            
                            @if($order->delivery->carrier)
                            <div class="mt-1">
                                <strong>Shipping Company:</strong> {{ $order->delivery->carrier }}
                                @php
                                    $trackingUrl = null;
                                    if ($order->delivery->carrier == 'UPS') {
                                        $trackingUrl = 'https://www.ups.com/track?tracknum=' . $order->delivery->tracking_number;
                                    } elseif ($order->delivery->carrier == 'FedEx') {
                                        $trackingUrl = 'https://www.fedex.com/fedextrack/?trknbr=' . $order->delivery->tracking_number;
                                    } elseif ($order->delivery->carrier == 'USPS') {
                                        $trackingUrl = 'https://tools.usps.com/go/TrackConfirmAction?tLabels=' . $order->delivery->tracking_number;
                                    } elseif ($order->delivery->carrier == 'DHL') {
                                        $trackingUrl = 'https://www.dhl.com/us-en/home/tracking/tracking-express.html?submit=1&tracking-id=' . $order->delivery->tracking_number;
                                    }
                                @endphp
                                
                                @if($trackingUrl)
                                <div class="mt-2">
                                    <a href="{{ $trackingUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-1"></i> Track on {{ $order->delivery->carrier }} Website
                                    </a>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($order->delivery->notes)
                    <div class="mb-3">
                        <strong>Delivery Instructions:</strong><br>
                        {{ $order->delivery->notes }}
                    </div>
                    @endif
                    
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
            @elseif($order->order_status !== 'cancelled')
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
                    
                    <form action="{{ route('orders.cancel', $order) }}" method="POST">
                        @csrf
                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this order?')">
                                Cancel Order
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

<style>
.status-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin: 0 auto;
}

.status-line {
    flex-grow: 1;
    height: 2px;
    background-color: #dee2e6;
    margin: 20px 10px;
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