@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ route('tracking.index') }}" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>Back to tracking
                </a>
                <div>
                    <span class="badge bg-light text-dark border p-2">
                        <i class="fas fa-hashtag me-1 text-primary"></i>
                        <span class="tracking-number">{{ $delivery->tracking_number }}</span>
                    </span>
                </div>
            </div>
            
            <!-- Status bar -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="mb-1">Order #{{ $order->id }}</h4>
                            <p class="text-muted mb-0">
                                Placed on {{ $order->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-warning',
                                    'assigned' => 'bg-info',
                                    'picked_up' => 'bg-info',
                                    'out_for_delivery' => 'bg-primary',
                                    'delivered' => 'bg-success',
                                    'failed' => 'bg-danger'
                                ];
                                $statusClass = $statusClasses[$delivery->delivery_status] ?? 'bg-secondary';
                                
                                $statusLabels = [
                                    'pending' => 'Pending',
                                    'assigned' => 'Assigned to Driver',
                                    'picked_up' => 'Picked Up',
                                    'out_for_delivery' => 'Out for Delivery',
                                    'delivered' => 'Delivered',
                                    'failed' => 'Delivery Failed'
                                ];
                                $statusLabel = $statusLabels[$delivery->delivery_status] ?? ucfirst($delivery->delivery_status);
                            @endphp
                            
                            <span class="badge {{ $statusClass }} fs-6 px-4 py-2">
                                {{ $statusLabel }}
                            </span>
                            
                            @if($delivery->estimated_delivery_date && $delivery->delivery_status != 'delivered')
                            <div class="mt-2 text-muted">
                                <i class="far fa-calendar-alt me-1"></i> Estimated delivery: 
                                <strong>{{ $delivery->estimated_delivery_date->format('M d, Y') }}</strong>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <!-- Tracking progress -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Delivery Progress</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="tracking-progress">
                                <div class="step {{ in_array($delivery->delivery_status, ['pending', 'assigned', 'picked_up', 'out_for_delivery', 'delivered']) ? 'active' : '' }}">
                                    <div class="step-icon"><i class="fas fa-clipboard-check"></i></div>
                                    <div class="step-text">Order Confirmed</div>
                                    <small>{{ $order->created_at->format('M d, Y') }}</small>
                                </div>
                                
                                <div class="step {{ in_array($delivery->delivery_status, ['assigned', 'picked_up', 'out_for_delivery', 'delivered']) ? 'active' : '' }}">
                                    <div class="step-icon"><i class="fas fa-box"></i></div>
                                    <div class="step-text">Processing</div>
                                </div>
                                
                                <div class="step {{ in_array($delivery->delivery_status, ['picked_up', 'out_for_delivery', 'delivered']) ? 'active' : '' }}">
                                    <div class="step-icon"><i class="fas fa-truck-loading"></i></div>
                                    <div class="step-text">Picked Up</div>
                                </div>
                                
                                <div class="step {{ in_array($delivery->delivery_status, ['out_for_delivery', 'delivered']) ? 'active' : '' }}">
                                    <div class="step-icon"><i class="fas fa-truck"></i></div>
                                    <div class="step-text">Out for Delivery</div>
                                </div>
                                
                                <div class="step {{ in_array($delivery->delivery_status, ['delivered']) ? 'active' : '' }}">
                                    <div class="step-icon"><i class="fas fa-home"></i></div>
                                    <div class="step-text">Delivered</div>
                                    @if($delivery->delivered_at)
                                    <small>{{ $delivery->delivered_at->format('M d, Y') }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order items summary -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach($order->orderItems as $item)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-muted">{{ $item->quantity }}x</span>
                                        {{ $item->product->name }}
                                    </div>
                                    <span>@baht($item->subtotal)</span>
                                </div>
                            </div>
                            @endforeach
                            <div class="list-group-item bg-light">
                                <div class="d-flex justify-content-between">
                                    <strong>Total:</strong>
                                    <strong>@baht($order->total_amount)</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Shipping information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Shipping Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="text-muted small">Recipient</div>
                                <div class="fs-5">{{ $delivery->recipient_name }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small">Phone</div>
                                <div>{{ $delivery->recipient_phone }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small">Address</div>
                                <div>{{ $delivery->recipient_address }}</div>
                            </div>
                            
                            @if($delivery->delivered_at)
                            <div class="mb-3">
                                <div class="text-muted small">Delivered On</div>
                                <div>{{ $delivery->delivered_at->format('M d, Y h:i A') }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Carrier information if available -->
                    @if($delivery->carrier)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Carrier Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="text-muted small">Shipping Company</div>
                                <div>{{ $delivery->carrier }}</div>
                            </div>
                            
                            @php
                                $trackingUrl = null;
                                if ($delivery->carrier == 'UPS') {
                                    $trackingUrl = 'https://www.ups.com/track?tracknum=' . $delivery->tracking_number;
                                } elseif ($delivery->carrier == 'FedEx') {
                                    $trackingUrl = 'https://www.fedex.com/fedextrack/?trknbr=' . $delivery->tracking_number;
                                } elseif ($delivery->carrier == 'USPS') {
                                    $trackingUrl = 'https://tools.usps.com/go/TrackConfirmAction?tLabels=' . $delivery->tracking_number;
                                } elseif ($delivery->carrier == 'DHL') {
                                    $trackingUrl = 'https://www.dhl.com/us-en/home/tracking/tracking-express.html?submit=1&tracking-id=' . $delivery->tracking_number;
                                }
                            @endphp
                            
                            @if($trackingUrl)
                            <div class="d-grid gap-2 mt-3">
                                <a href="{{ $trackingUrl }}" target="_blank" class="btn btn-outline-primary">
                                    <i class="fas fa-external-link-alt me-2"></i>Track on {{ $delivery->carrier }} Website
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('tracking.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-search me-2"></i>Track Another Order
                </a>
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>Return to Homepage
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.tracking-progress {
    margin-top: 20px;
    padding: 0 15px;
}

.tracking-progress .step {
    position: relative;
    padding-left: 50px;
    margin-bottom: 30px;
    opacity: 0.5;
    color: #6c757d;
}

.tracking-progress .step.active {
    opacity: 1;
    color: #212529;
}

.tracking-progress .step:last-child {
    margin-bottom: 0;
}

.tracking-progress .step:before {
    content: '';
    position: absolute;
    left: 16px;
    top: 30px;
    height: calc(100% + 0px);
    width: 2px;
    background-color: #dee2e6;
}

.tracking-progress .step:last-child:before {
    display: none;
}

.tracking-progress .step-icon {
    position: absolute;
    left: 0;
    top: 0;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}

.tracking-progress .step.active .step-icon {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.tracking-progress .step-text {
    font-weight: 500;
    font-size: 16px;
    margin-bottom: 2px;
}

.tracking-progress .step small {
    display: block;
    font-size: 12px;
    color: #6c757d;
}

.tracking-number {
    font-family: monospace;
    font-size: 1rem;
    letter-spacing: 0.5px;
}
</style>
@endsection 