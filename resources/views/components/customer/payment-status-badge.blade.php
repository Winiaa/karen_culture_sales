@props(['order'])
@inject('statusService', 'App\Services\OrderStatusService')

@php
    $badgeColor = $statusService->getPaymentStatusBadgeColor(
        $order->payment_status, 
        $order->order_status, 
        $order->payment ? $order->payment->payment_method : null
    );

    $statusText = $statusService->getPaymentStatusText(
        $order->payment_status, 
        $order->order_status, 
        $order->payment ? $order->payment->payment_method : null
    );
@endphp

<span class="badge bg-{{ $badgeColor }} rounded-pill">
    {{ $statusText }}
</span> 