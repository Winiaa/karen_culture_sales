@props(['order'])
@inject('statusService', 'App\Services\OrderStatusService')

@php
    $badgeColor = $statusService->getPaymentStatusBadgeColor(
        $order->payment_status, 
        $order->order_status, 
        $order->payment ? $order->payment->payment_method : null
    );

    $statusText = match(true) {
        $order->order_status === 'cancelled' && $order->payment && $order->payment->payment_method === 'stripe' => 'Refunded',
        $order->order_status === 'cancelled' => 'Cancelled',
        default => ucfirst($order->payment_status)
    };
@endphp

<span class="badge bg-{{ $badgeColor }}">
    {{ $statusText }}
</span> 