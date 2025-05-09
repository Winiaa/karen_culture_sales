@props(['order'])
@inject('statusService', 'App\Services\OrderStatusService')

@php
    $paymentStatus = $order->payment ? $order->payment->payment_status : 'pending';
    $orderStatusColor = $statusService->getOrderStatusBadgeColor($order->order_status);
    $paymentStatusColor = $statusService->getPaymentStatusBadgeColor(
        $paymentStatus,
        $order->order_status,
        $order->payment ? $order->payment->payment_method : null
    );
@endphp

<div class="d-flex justify-content-between mb-4">
    {{-- Order Placed --}}
    <div class="text-center">
        <div class="status-circle {{ $order->order_status != 'cancelled' ? 'bg-success' : 'bg-danger' }}">
            <i class="fas fa-check"></i>
        </div>
        <p class="mt-2 mb-0">Order Placed</p>
        <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
    </div>

    <div class="status-line" data-status-color="{{ $statusService->getOrderStatusLineColor($order->order_status) }}"></div>

    {{-- Payment --}}
    <div class="text-center">
        <div class="status-circle bg-{{ $paymentStatusColor }}">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <p class="mt-2 mb-0">Payment</p>
        <small class="text-muted">
            @if($order->order_status === 'cancelled')
                @if($order->payment && $order->payment->payment_method === 'stripe')
                    Refunded
                @else
                    Cancelled
                @endif
            @else
                {{ ucfirst($paymentStatus) }}
            @endif
        </small>
    </div>

    <div class="status-line" data-status-color="{{ $statusService->getPaymentStatusLineColor($paymentStatus, $order->order_status, $order->payment ? $order->payment->payment_method : null) }}"></div>

    {{-- Shipped --}}
    <div class="text-center">
        <div class="status-circle {{ $order->order_status === 'cancelled' ? 'bg-danger' : ($order->order_status === 'shipped' || $order->order_status === 'delivered' ? 'bg-success' : 'bg-secondary') }}">
            <i class="fas fa-shipping-fast"></i>
        </div>
        <p class="mt-2 mb-0">Shipped</p>
        @if($order->order_status === 'cancelled')
            <small class="text-muted">Cancelled</small>
        @elseif($order->delivery && ($order->order_status === 'shipped' || $order->order_status === 'delivered'))
            <small class="text-muted">{{ $order->delivery->updated_at->format('M d, Y') }}</small>
        @endif
    </div>

    <div class="status-line" data-status-color="{{ $statusService->getOrderStatusLineColor($order->order_status) }}"></div>

    {{-- Delivered --}}
    <div class="text-center">
        <div class="status-circle {{ $order->order_status === 'cancelled' ? 'bg-danger' : ($order->order_status === 'delivered' ? 'bg-success' : 'bg-secondary') }}">
            <i class="fas fa-home"></i>
        </div>
        <p class="mt-2 mb-0">Delivered</p>
        @if($order->order_status === 'cancelled')
            <small class="text-muted">Cancelled</small>
        @elseif($order->delivery && $order->delivery->delivery_status === 'delivered')
            <small class="text-muted">{{ $order->delivery->delivered_at ? $order->delivery->delivered_at->format('M d, Y') : $order->delivery->updated_at->format('M d, Y') }}</small>
        @endif
    </div>
</div>

@push('styles')
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
    margin: 20px 10px;
    background-color: var(--status-color, #e9ecef);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.status-line').forEach(function(line) {
        const color = line.dataset.statusColor;
        line.style.setProperty('--status-color', color);
    });
});
</script>
@endpush 