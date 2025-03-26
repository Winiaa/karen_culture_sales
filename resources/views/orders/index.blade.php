@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">My Orders</h1>
    
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    @forelse($orders as $order)
    <div class="card mb-4">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Order #{{ $order->id }}</h5>
                    <small class="text-muted">Placed on {{ $order->created_at->format('M d, Y') }}</small>
                </div>
                <div class="col-auto">
                    <span class="badge bg-{{ $order->order_status === 'delivered' ? 'success' : ($order->order_status === 'cancelled' ? 'danger' : 'primary') }}">
                        {{ ucfirst($order->order_status) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    @foreach($order->orderItems as $item)
                    <div class="d-flex mb-3">
                        <img src="{{ Storage::url($item->product->image) }}" class="rounded" alt="{{ $item->product->title }}" style="width: 80px; height: 80px; object-fit: cover;">
                        <div class="ms-3">
                            <h6 class="mb-1">{{ $item->product->title }}</h6>
                            <p class="mb-1 text-muted">
                                Quantity: {{ $item->quantity }} × @baht($item->product->final_price)
                            </p>
                            <p class="mb-0">Subtotal: @baht($item->subtotal)</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="col-md-4">
                    <div class="border-start ps-4">
                        <p class="mb-2">
                            <strong>Total Amount:</strong>
                            @baht($order->total_amount)
                        </p>
                        <div class="fw-bold text-secondary text-uppercase small">
                            Payment Method
                        </div>
                        <div>{{ $order->payment ? ($order->payment->payment_method === 'stripe' ? 'Credit Card' : 'Cash on Delivery') : 'N/A' }}</div>
                        <p class="mb-2">
                            <strong>Payment Status:</strong>
                            <span class="badge bg-{{ $order->payment_status === 'completed' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'warning') }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </p>
                        @if($order->delivery && $order->delivery->tracking_number)
                        <p class="mb-2">
                            <strong>Tracking Number:</strong>
                            <span class="d-block">{{ $order->delivery->tracking_number }}</span>
                        </p>
                        <p class="mb-2">
                            <strong>Shipping Status:</strong>
                            <span class="badge bg-{{ $order->delivery->delivery_status === 'delivered' ? 'success' : ($order->delivery->delivery_status === 'out_for_delivery' ? 'info' : 'warning') }}">
                                {{ $order->delivery->delivery_status === 'delivered' ? 'Delivered' : ($order->delivery->delivery_status === 'out_for_delivery' ? 'In Transit' : 'Processing') }}
                            </span>
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-karen">
                    View Details
                </a>
                @if($order->canBeCancelled())
                    @if($order->payment && $order->payment->payment_method === 'stripe' && $order->paid_at)
                        <div class="d-flex align-items-center me-3">
                            <span class="badge bg-info text-dark">
                                <i class="fas fa-clock"></i> 
                                {{ $order->getRemainingCancellationTime() }} min left to cancel
                            </span>
                        </div>
                    @endif
                    <form action="{{ route('orders.cancel', $order) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this order?')">
                            Cancel Order
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="alert alert-info">
        You haven't placed any orders yet. <a href="{{ route('products.index') }}" class="alert-link">Start shopping</a>
    </div>
    @endforelse

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $orders->links() }}
    </div>
</div>
@endsection

@section('styles')
<style>
.bg-karen {
    background-color: #8D6E63;
    color: white;
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

/* Pagination customization */
.pagination {
    gap: 5px;
}

.page-link {
    border-radius: 4px;
    padding: 8px 12px;
    color: #666;
    border: 1px solid #dee2e6;
    min-width: 36px;
    text-align: center;
}

.page-link:hover {
    color: #8D6E63;
    border-color: #8D6E63;
    background-color: #fff;
}

.page-link:focus {
    box-shadow: 0 0 0 0.2rem rgba(141, 110, 99, 0.25);
}

.page-item.active .page-link {
    background-color: #8D6E63;
    border-color: #8D6E63;
    color: white;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

.fas {
    font-size: 12px;
}
</style>

<!-- Add Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection 