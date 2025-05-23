@props(['order'])
@inject('statusService', 'App\Services\OrderStatusService')

@php
    $paymentStatus = $order->payment ? $order->payment->payment_status : 'pending';
    
    $badgeColor = $statusService->getPaymentStatusBadgeColor(
        $paymentStatus, 
        $order->order_status, 
        $order->payment ? $order->payment->payment_method : null
    );

    $statusText = match(true) {
        $order->order_status === 'cancelled' && $order->payment && $order->payment->payment_method === 'stripe' => 'Refunded',
        $order->order_status === 'cancelled' => 'Cancelled',
        default => ucfirst($paymentStatus)
    };
@endphp

<span class="badge bg-{{ $badgeColor }}">
    {{ $statusText }}
</span> 