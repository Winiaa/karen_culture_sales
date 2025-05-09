@extends('layouts.admin')

@section('title', 'Order Details')
@section('subtitle', 'View and manage order information')

@push('styles')
<style>
.timeline {
    position: relative;
    padding: 0;
    list-style: none;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    left: 20px;
    height: 100%;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    padding-left: 50px;
    padding-bottom: 30px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-item i {
    position: absolute;
    left: 10px;
    width: 20px;
    height: 20px;
    text-align: center;
    color: #fff;
    background: #e9ecef;
    border-radius: 50%;
    padding: 4px;
    font-size: 12px;
}

.timeline-item.completed i {
    background: #28a745;
}

.timeline-item.failed i {
    background: #dc3545;
}

.timeline-item.info i {
    background: #0dcaf0;
}

/* Add status line colors */
.timeline-item.completed:after {
    content: '';
    position: absolute;
    left: 20px;
    top: 30px;
    height: calc(100% - 30px);
    width: 2px;
    background: #28a745;
}

.timeline-item.failed:after {
    content: '';
    position: absolute;
    left: 20px;
    top: 30px;
    height: calc(100% - 30px);
    width: 2px;
    background: #dc3545;
}

.timeline-item.info:after {
    content: '';
    position: absolute;
    left: 20px;
    top: 30px;
    height: calc(100% - 30px);
    width: 2px;
    background: #0dcaf0;
}

.timeline-content {
    padding: 0;
}

.timeline-content h6 {
    margin-bottom: 0.25rem;
}

/* Add styles for cancelled status */
.timeline-item.failed .timeline-content h6 {
    color: #dc3545;
}

.timeline-item.failed .timeline-content p.text-muted {
    color: #dc3545 !important;
}

.driver-list-item {
    cursor: pointer;
    border-radius: 5px;
    transition: all 0.2s;
}

.driver-list-item:hover {
    background-color: #f8f9fa;
}

.driver-list-item.selected {
    background-color: #e8f4f8;
    border-left: 3px solid var(--primary-color);
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
            <li class="breadcrumb-item active">Order #{{ $order->id }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Order Status Timeline -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Order Status</h5>
                    <div class="timeline">
                        <div class="timeline-item {{ $order->created_at ? 'completed' : '' }}">
                            <i class="fas fa-shopping-cart"></i>
                            <div class="timeline-content">
                                <h6>Order Placed</h6>
                                <p class="text-muted">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        <div class="timeline-item {{ $order->order_status === 'cancelled' ? ($order->payment && $order->payment->payment_method === 'stripe' ? 'info' : 'failed') : ($order->payment && $order->payment->payment_status === 'completed' ? 'completed' : '') }}">
                            @if($order->order_status === 'cancelled')
                                @if($order->payment && $order->payment->payment_method === 'stripe')
                                    <i class="fas fa-undo"></i>
                                @else
                                    <i class="fas fa-times-circle"></i>
                                @endif
                            @else
                                <i class="fas fa-credit-card"></i>
                            @endif
                            <div class="timeline-content">
                                <h6>
                                    Payment {{ $order->order_status === 'cancelled' ? ($order->payment && $order->payment->payment_method === 'stripe' ? 'Refunded' : 'Cancelled') : ($order->payment ? ucfirst($order->payment->payment_status) : 'Pending') }}
                                </h6>
                                <p class="text-muted">{{ $order->payment ? $order->payment->updated_at->format('M d, Y h:i A') : 'Not available' }}</p>
                            </div>
                        </div>
                        <div class="timeline-item {{ $order->order_status === 'cancelled' ? 'failed' : ($order->order_status === 'shipped' || $order->order_status === 'delivered' ? 'completed' : '') }}">
                            <i class="fas fa-truck"></i>
                            <div class="timeline-content">
                                <h6>Order Shipped</h6>
                                <p class="text-muted">
                                    @if($order->order_status === 'cancelled')
                                        Cancelled
                                    @elseif($order->order_status === 'shipped' || $order->order_status === 'delivered')
                                        @if($order->delivery)
                                            {{ $order->delivery->updated_at->format('M d, Y h:i A') }}
                                        @else
                                            {{ now()->format('M d, Y h:i A') }}
                                        @endif
                                    @else
                                        Pending
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="timeline-item {{ $order->order_status === 'cancelled' ? 'failed' : ($order->order_status === 'delivered' ? 'completed' : '') }}">
                            <i class="fas fa-box"></i>
                            <div class="timeline-content">
                                <h6>Order Delivered</h6>
                                <p class="text-muted">
                                    @if($order->order_status === 'cancelled')
                                        Cancelled
                                    @elseif($order->order_status === 'delivered')
                                        @if($order->delivery && $order->delivery->delivered_at)
                                            {{ $order->delivery->delivered_at->format('M d, Y h:i A') }}
                                        @else
                                            {{ now()->format('M d, Y h:i A') }}
                                        @endif
                                    @else
                                        Pending
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order Items</h5>
                    <span class="badge bg-{{ $order->order_status === 'delivered' ? 'success' : ($order->order_status === 'cancelled' ? 'danger' : ($order->order_status === 'processing' ? 'warning' : 'primary')) }}">
                        {{ ucfirst($order->order_status) }}
                    </span>
                </div>
                <div class="card-body">
                    @foreach($order->orderItems as $item)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->title }}" class="rounded" style="width: 64px; height: 64px; object-fit: cover;">
                        <div class="ms-3 flex-grow-1">
                            <h6 class="mb-1">{{ $item->product->title }}</h6>
                            <p class="text-muted mb-0">
                                Category: {{ $item->product->category->name }} |
                                Quantity: {{ $item->quantity }}
                            </p>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">@baht($item->price * $item->quantity)</div>
                            <small class="text-muted">@baht($item->price) each</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Delivery Information</h5>
                    @if($order->delivery)
                        <span class="badge bg-{{ $order->order_status === 'cancelled' ? 'danger' : ($order->delivery->delivery_status === 'delivered' ? 'success' : ($order->delivery->delivery_status === 'failed' ? 'danger' : ($order->delivery->delivery_status === 'pending' ? 'warning' : 'primary'))) }}">
                            {{ $order->order_status === 'cancelled' ? 'Cancelled' : ucfirst(str_replace('_', ' ', $order->delivery->delivery_status)) }}
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    <!-- Driver Assignment Section -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Driver Assignment</h6>
                        @if($order->delivery && $order->delivery->driver)
                            <div class="p-3 bg-light rounded mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary text-white rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">{{ $order->delivery->driver->user->name }}</h6>
                                        <p class="text-muted mb-0">{{ $order->delivery->driver->phone_number }}</p>
                                        <span class="badge bg-info">{{ ucfirst($order->delivery->driver->vehicle_type) }}</span>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#assignDriverModal">
                                            Change Driver
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i> No driver assigned yet
                                <div class="mt-2">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignDriverModal">
                                        <i class="fas fa-truck me-1"></i> Assign Driver
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('admin.orders.delivery.update', $order) }}" method="POST" id="deliveryForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <!-- Required Fields Section -->
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">Recipient Information</h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Recipient Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="recipient_name" value="{{ $order->delivery?->recipient_name ?? $order->user->name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Recipient Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="recipient_phone" value="{{ $order->delivery?->recipient_phone ?? $order->user->phone }}" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Delivery Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="recipient_address" rows="2" required>{{ $order->delivery?->recipient_address ?? "$order->shipping_address, $order->shipping_city, $order->shipping_state, $order->shipping_country $order->shipping_zip" }}</textarea>
                            </div>

                            <!-- Delivery Details Section -->
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3 mt-4">Delivery Details</h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Delivery Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="delivery_status" required>
                                    <option value="pending" {{ $order->delivery?->delivery_status == 'pending' ? 'selected' : '' }}>Processing</option>
                                    <option value="assigned" {{ $order->delivery?->delivery_status == 'assigned' ? 'selected' : '' }}>Assigned to Driver</option>
                                    <option value="picked_up" {{ $order->delivery?->delivery_status == 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                                    <option value="out_for_delivery" {{ $order->delivery?->delivery_status == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                    <option value="delivered" {{ $order->delivery?->delivery_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="failed" {{ $order->delivery?->delivery_status == 'failed' ? 'selected' : '' }}>Delivery Failed</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estimated Delivery Date</label>
                                <input type="date" class="form-control" name="estimated_delivery_date" value="{{ $order->delivery?->estimated_delivery_date?->format('Y-m-d') ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tracking Number</label>
                                <input type="text" class="form-control" value="{{ $order->delivery?->tracking_number ?? '' }}" readonly>
                                <small class="text-muted">System-generated tracking number for internal delivery tracking</small>
                            </div>
                            
                            <!-- Delivery Confirmation Details -->
                            @if($order->delivery && ($order->delivery->delivery_photo || $order->delivery->delivery_notes))
                            <div class="col-12 mb-3">
                                <hr>
                                <h6 class="text-primary mb-3">Delivery Confirmation Information</h6>
                                <div class="row">
                                    @if($order->delivery->delivery_photo)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Delivery Photo:</label>
                                        <div>
                                            <a href="{{ asset('storage/' . $order->delivery->delivery_photo) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $order->delivery->delivery_photo) }}" 
                                                    alt="Delivery Photo" class="img-thumbnail" style="max-height: 200px;">
                                            </a>
                                        </div>
                                        <small class="text-muted">Click to view full size</small>
                                    </div>
                                    @endif
                                    
                                    @if($order->delivery->delivery_notes)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Driver's Notes:</label>
                                        <div class="alert alert-light">
                                            {{ $order->delivery->delivery_notes }}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-karen">Update Delivery Information</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Name:</strong> {{ $order->user->name }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $order->user->email }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $order->user->default_recipient_phone ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Payment Details Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-money-bill me-2"></i>Payment Details
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Order Summary -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Order Summary</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Subtotal:</small>
                                <span>฿{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Shipping Fee:</small>
                                <span>
                                    @if($order->subtotal >= config('shipping.free_shipping_threshold'))
                                        <span class="text-success">Free</span>
                                    @else
                                        ฿{{ number_format($order->shipping_cost, 2) }}
                                    @endif
                                </span>
                            </div>
                            @if($order->discount_amount > 0)
                            <div class="col-6">
                                <small class="text-muted d-block">Discount:</small>
                                <span class="text-danger">-฿{{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                            @endif
                            <div class="col-6">
                                <small class="text-muted d-block">Total Amount:</small>
                                <span class="fs-5 fw-bold">฿{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method & Status -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Payment Information</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Payment Method:</small>
                                <div>
                                    @if($order->payment)
                                        @if($order->payment->payment_method === 'cash_on_delivery')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-money-bill me-1"></i> Cash on Delivery
                                            </span>
                                        @elseif($order->payment->payment_method === 'stripe')
                                            <span class="badge bg-info">
                                                <i class="fab fa-stripe me-1"></i> Stripe
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Not specified</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Payment Status:</small>
                                <div>
                                    @php
                                        $statusConfig = [
                                            'cancelled' => [
                                                'stripe' => ['class' => 'bg-info', 'icon' => 'undo', 'text' => 'Refunded'],
                                                'default' => ['class' => 'bg-danger', 'icon' => 'times-circle', 'text' => 'Cancelled']
                                            ],
                                            'completed' => ['class' => 'bg-success', 'icon' => 'check-circle', 'text' => 'Paid'],
                                            'pending' => ['class' => 'bg-warning', 'icon' => 'clock', 'text' => 'Pending'],
                                            'refunded' => ['class' => 'bg-info', 'icon' => 'undo', 'text' => 'Refunded']
                                        ];
                                        
                                        $paymentStatus = $order->payment ? $order->payment->payment_status : 'pending';
                                        
                                        $status = $order->order_status === 'cancelled' 
                                            ? ($order->payment && $order->payment->payment_method === 'stripe' 
                                                ? $statusConfig['cancelled']['stripe'] 
                                                : $statusConfig['cancelled']['default'])
                                            : ($statusConfig[$paymentStatus] ?? ['class' => 'bg-secondary', 'icon' => 'question-circle', 'text' => 'Unknown']);
                                    @endphp
                                    <span class="badge {{ $status['class'] }}">
                                        <i class="fas fa-{{ $status['icon'] }} me-1"></i> {{ $status['text'] }}
                                    </span>
                                </div>
                            </div>
                            @if($order->payment && $order->payment->transaction_id)
                            <div class="col-12">
                                <small class="text-muted d-block">Transaction ID:</small>
                                <code>{{ $order->payment->transaction_id }}</code>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Cancelled Order Details -->
                    @if($order->order_status === 'cancelled')
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Cancellation Details</h6>
                            @php
                                $isStripePayment = $order->payment && $order->payment->payment_method === 'stripe';
                                $alertClass = $isStripePayment ? 'alert-info' : 'alert-danger';
                                $icon = $isStripePayment ? 'undo' : 'times-circle';
                                $title = $isStripePayment ? 'Payment Refunded' : 'Order Cancelled';
                            @endphp
                            <div class="alert {{ $alertClass }}">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="fas fa-{{ $icon }} fa-2x"></i>
                                    </div>
                                    <div>
                                        <h6 class="alert-heading mb-1">{{ $title }}</h6>
                                        <p class="mb-0">
                                            @if($isStripePayment)
                                                The payment has been refunded to the customer's card.
                                                @if($order->payment->refunded_at)
                                                    <br>
                                                    <small>Refunded on {{ $order->payment->refunded_at->format('M d, Y h:i A') }}</small>
                                                @endif
                                            @else
                                                This order was cancelled before payment was collected.
                                            @endif
                                        </p>
                                        @if($isStripePayment && $order->payment->refund_id)
                                            <div class="mt-2">
                                                <small class="text-muted">Refund ID:</small>
                                                <code>{{ $order->payment->refund_id }}</code>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- COD Payment Details -->
                    @if($order->payment && $order->payment->payment_method === 'cash_on_delivery' && $order->delivery && $order->order_status !== 'cancelled')
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Collection Details</h6>
                            <div class="mt-2">
                                @if($order->delivery->payment_status === 'received')
                                    <div class="alert alert-success mb-3">
                                        <div class="d-flex">
                                            <div class="me-3">
                                                <i class="fas fa-check-circle fa-2x"></i>
                                            </div>
                                            <div>
                                                <h6 class="alert-heading mb-1">Payment Collected</h6>
                                                <p class="mb-0">
                                                    @if($order->delivery->transfer_proof)
                                                        Paid by bank transfer
                                                    @else
                                                        Paid in cash
                                                    @endif
                                                </p>
                                                <small>
                                                    Collected on {{ \Carbon\Carbon::parse($order->delivery->payment_received_at)->format('M d, Y h:i A') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    @if($order->delivery->payment_notes)
                                        <div class="mb-3">
                                            <small class="text-muted d-block">Payment Notes:</small>
                                            <div class="p-3 bg-light rounded">
                                                {{ $order->delivery->payment_notes }}
                                            </div>
                                        </div>
                                    @endif

                                    @if($order->delivery->transfer_proof)
                                        <div>
                                            <small class="text-muted d-block mb-2">Transfer Slip:</small>
                                            <a href="{{ Storage::url($order->delivery->transfer_proof) }}" 
                                               target="_blank" 
                                               class="d-inline-block">
                                                <img src="{{ Storage::url($order->delivery->transfer_proof) }}" 
                                                     alt="Transfer Proof" 
                                                     class="img-fluid rounded shadow-sm"
                                                     style="max-height: 150px;">
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <div class="alert alert-warning mb-0">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock me-2"></i>
                                            <div>Payment collection pending</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Stripe Payment Details -->
                    @if($order->payment && $order->payment->payment_method === 'stripe')
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Stripe Payment Details</h6>
                            <div class="row g-3">
                                @if($order->payment->card_brand && $order->payment->card_last4)
                                <div class="col-6">
                                    <small class="text-muted d-block">Card Type:</small>
                                    <span>
                                        <i class="fab fa-cc-{{ strtolower($order->payment->card_brand) }} me-1"></i>
                                        {{ ucfirst($order->payment->card_brand) }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Card Number:</small>
                                    <span>**** **** **** {{ $order->payment->card_last4 }}</span>
                                </div>
                                @endif
                                @if($order->payment->receipt_url)
                                <div class="col-12">
                                    <small class="text-muted d-block">Receipt:</small>
                                    <a href="{{ $order->payment->receipt_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-receipt me-1"></i> View Receipt
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Payment Timestamp -->
                    @if($order->payment)
                        <div class="mt-3 pt-3 border-top">
                            <small class="text-muted">
                                <i class="far fa-clock me-1"></i>
                                @if($order->payment->payment_status === 'completed' && $order->payment->paid_at)
                                    Payment completed on {{ $order->payment->paid_at->format('M d, Y h:i A') }}
                                @elseif($order->payment->payment_status === 'refunded' && $order->payment->refunded_at)
                                    Payment refunded on {{ $order->payment->refunded_at->format('M d, Y h:i A') }}
                                @elseif($order->payment->updated_at)
                                    Last updated on {{ $order->payment->updated_at->format('M d, Y h:i A') }}
                                @else
                                    Payment information available
                                @endif
                            </small>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Driver Information (if assigned) -->
            @if($order->delivery && $order->delivery->driver)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Assigned Driver</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-white rounded-circle p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ $order->delivery->driver->user->name }}</h6>
                            <p class="text-muted mb-0">{{ $order->delivery->driver->phone_number }}</p>
                            <p class="mb-0">
                                <span class="badge bg-{{ $order->delivery->driver->is_active ? 'success' : 'secondary' }}">
                                    {{ $order->delivery->driver->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <span class="badge bg-info">{{ ucfirst($order->delivery->driver->vehicle_type) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Assign Driver Modal -->
<div class="modal fade" id="assignDriverModal" tabindex="-1" aria-labelledby="assignDriverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignDriverModalLabel">Assign Driver to Order #{{ $order->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.orders.assign-driver', $order) }}" method="POST" id="assignDriverForm">
                @csrf
                <div class="modal-body">
                    <p class="mb-3">Select a driver to handle the delivery of this order:</p>
                    
                    <div class="mb-3">
                        <input type="text" class="form-control" id="driverSearch" placeholder="Search for a driver...">
                    </div>
                    
                    <div class="driver-list" style="max-height: 400px; overflow-y: auto;">
                        @php
                            $drivers = \App\Models\Driver::with('user')
                                ->where('is_active', true)
                                ->get();
                        @endphp
                        
                        @if($drivers->isEmpty())
                            <div class="alert alert-warning">
                                No active drivers available. Please add drivers or activate existing ones.
                                <a href="{{ route('admin.drivers.create') }}" class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-plus"></i> Add New Driver
                                </a>
                            </div>
                        @else
                            @foreach($drivers as $driver)
                                <div class="card mb-2 driver-list-item {{ $order->delivery && $order->delivery->driver_id == $driver->id ? 'selected' : '' }}" data-driver-id="{{ $driver->id }}" data-driver-name="{{ $driver->user->name }}">
                                    <div class="card-body p-3">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <div class="form-check">
                                                    <input class="form-check-input driver-radio" type="radio" name="driver_id" id="driver{{ $driver->id }}" value="{{ $driver->id }}" {{ $order->delivery && $order->delivery->driver_id == $driver->id ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="driver{{ $driver->id }}"></label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <h6 class="mb-0">{{ $driver->user->name }}</h6>
                                                <small class="text-muted">{{ $driver->phone_number }}</small>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="badge bg-info">{{ ucfirst($driver->vehicle_type) }}</span>
                                                <small class="d-block text-muted">{{ $driver->vehicle_plate }}</small>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="d-block"><strong>Active Deliveries:</strong> {{ $driver->activeDeliveries()->count() }}</small>
                                                <small class="d-block"><strong>Completed:</strong> {{ $driver->completedDeliveries()->count() }}</small>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <a href="{{ route('admin.drivers.show', $driver) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                                    <i class="fas fa-external-link-alt"></i> View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-karen" {{ $drivers->isEmpty() ? 'disabled' : '' }}>Assign Driver</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Only fix the status update modal
        const statusBtn = document.getElementById('openStatusModal');
        const statusModal = document.getElementById('updateStatusModal{{ $order->id }}');
        
        if (statusBtn && statusModal) {
            // Clean up function for modal artifacts
            function cleanupModalArtifacts() {
                // Remove any excess backdrop elements
                const extraBackdrops = document.querySelectorAll('.modal-backdrop:not(:first-child)');
                extraBackdrops.forEach(function(backdrop) {
                    backdrop.remove();
                });
                
                // Reset body styles that may be left over
                document.body.style.paddingRight = '';
            }
            
            // Handle status modal manually to prevent flickering
            statusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Cleanup first
                cleanupModalArtifacts();
                
                // Using vanilla Bootstrap to show the modal
                const bsModal = new bootstrap.Modal(statusModal);
                
                // Small delay helps prevent flickering
                setTimeout(function() {
                    bsModal.show();
                }, 50);
            });
            
            // Add event listener to clean up when the modal is hidden
            statusModal.addEventListener('hidden.bs.modal', cleanupModalArtifacts);
        }

        // Handle carrier selection and "Other" option
        const carrierSelect = document.querySelector('select[name="carrier"]');
        const otherCarrierContainer = document.getElementById('other-carrier-container');
        const otherCarrierInput = document.getElementById('other-carrier');
        
        if (carrierSelect) {
            carrierSelect.addEventListener('change', function() {
                if (this.value === 'Other') {
                    otherCarrierContainer.classList.remove('d-none');
                    otherCarrierInput.setAttribute('required', 'required');
                } else {
                    otherCarrierContainer.classList.add('d-none');
                    otherCarrierInput.removeAttribute('required');
                }
            });
        }

        // Enhanced driver list item handling
        const driverListItems = document.querySelectorAll('.driver-list-item');
        const driverRadios = document.querySelectorAll('.driver-radio');
        
        // Function to update driver selection UI
        function updateDriverSelection(selectedId) {
            // Update all list items
            driverListItems.forEach(function(item) {
                if (item.dataset.driverId === selectedId) {
                    item.classList.add('selected');
                } else {
                    item.classList.remove('selected');
                }
            });
            
            // Ensure the correct radio button is checked
            driverRadios.forEach(function(radio) {
                radio.checked = (radio.value === selectedId);
            });
        }
        
        // Handle clicks on driver list items
        driverListItems.forEach(function(item) {
            item.addEventListener('click', function() {
                const driverId = this.dataset.driverId;
                updateDriverSelection(driverId);
            });
        });
        
        // Handle clicks on driver radio buttons
        driverRadios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    updateDriverSelection(this.value);
                }
            });
        });
        
        // Handle driver search
        const driverSearch = document.getElementById('driverSearch');
        if (driverSearch) {
            driverSearch.addEventListener('keyup', function() {
                const query = this.value.toLowerCase();
                
                driverListItems.forEach(function(item) {
                    const driverName = item.dataset.driverName.toLowerCase();
                    
                    if (driverName.includes(query)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }

        // Driver assignment form handling
        const assignDriverForm = document.getElementById('assignDriverForm');
        const assignDriverModal = document.getElementById('assignDriverModal');
        
        if (assignDriverForm) {
            assignDriverForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent default submission
                
                // Make sure a driver is selected
                const selectedDriver = assignDriverForm.querySelector('input[name="driver_id"]:checked');
                if (!selectedDriver) {
                    alert('Please select a driver to assign to this order.');
                    return false;
                }
                
                // Disable the submit button to prevent multiple submissions
                const submitButton = assignDriverForm.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Assigning...';
                
                // Perform AJAX form submission
                fetch(assignDriverForm.action, {
                    method: 'POST',
                    body: new FormData(assignDriverForm),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Failed to assign driver');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Hide the modal
                        if (assignDriverModal) {
                            const bsModal = bootstrap.Modal.getInstance(assignDriverModal);
                            if (bsModal) bsModal.hide();
                        }
                        
                        // Show success message
                        const successMessage = document.createElement('div');
                        successMessage.className = 'alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3';
                        successMessage.style.zIndex = '9999';
                        successMessage.innerHTML = data.message + ' <span class="ms-2">Refreshing page...</span>';
                        document.body.appendChild(successMessage);
                        
                        // Reload the page after a brief delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        throw new Error(data.message || 'Failed to assign driver');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    // Re-enable the submit button
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Assign Driver';
                    
                    // Show error message in a more visible way
                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'alert alert-danger position-fixed top-0 start-50 translate-middle-x mt-3';
                    errorMessage.style.zIndex = '9999';
                    errorMessage.textContent = error.message || 'An error occurred while assigning the driver. Please try again.';
                    document.body.appendChild(errorMessage);
                    
                    // Remove the error message after 5 seconds
                    setTimeout(() => {
                        errorMessage.remove();
                    }, 5000);
                });
            });
        }

        // Ensure the delivery form submits correctly
        const deliveryForm = document.getElementById('deliveryForm');
        if (deliveryForm) {
            deliveryForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Handle the "Other" carrier option
                if (carrierSelect && carrierSelect.value === 'Other' && otherCarrierInput && otherCarrierInput.value.trim()) {
                    // Create a hidden input for the custom carrier
                    const hiddenCarrierInput = document.createElement('input');
                    hiddenCarrierInput.type = 'hidden';
                    hiddenCarrierInput.name = 'carrier';
                    hiddenCarrierInput.value = otherCarrierInput.value.trim();
                    
                    // Replace the carrier select with the hidden input
                    carrierSelect.removeAttribute('name');
                    deliveryForm.appendChild(hiddenCarrierInput);
                }
                
                // Create a FormData object from the form
                const formData = new FormData(deliveryForm);
                
                // Disable the submit button to prevent multiple submissions
                const submitButton = deliveryForm.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';
                
                // Get the CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Send the form data via AJAX
                fetch(deliveryForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Server error');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Success:', data);
                    
                    // Re-enable the submit button
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Update Delivery Information';
                    
                    // Show success message
                    const successMessage = document.createElement('div');
                    successMessage.className = 'alert alert-success mt-3';
                    successMessage.textContent = data.message || 'Delivery information updated successfully!';
                    deliveryForm.appendChild(successMessage);
                    
                    // Remove the message after 3 seconds
                    setTimeout(() => {
                        successMessage.remove();
                    }, 3000);
                    
                    // Reload the page after a brief delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    // Re-enable the submit button
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Update Delivery Information';
                    
                    // Show error message with details if available
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger mt-3';
                    
                    if (error.response && error.response.data && error.response.data.errors) {
                        // Handle validation errors
                        const errorList = document.createElement('ul');
                        errorList.className = 'mb-0 list-unstyled';
                        
                        Object.entries(error.response.data.errors).forEach(([field, messages]) => {
                            messages.forEach(message => {
                                const li = document.createElement('li');
                                li.textContent = message;
                                errorList.appendChild(li);
                            });
                        });
                        
                        errorDiv.appendChild(errorList);
                    } else {
                        errorDiv.textContent = error.message || 'An error occurred. Please try again.';
                    }
                    
                    // Add the error div to the form
                    deliveryForm.appendChild(errorDiv);
                    
                    // Remove the error message after 5 seconds
                    setTimeout(() => {
                        errorDiv.remove();
                    }, 5000);
                });
            });
        }
    });
</script>
@endpush

@endsection 