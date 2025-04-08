@props(['order'])
@inject('statusService', 'App\Services\OrderStatusService')

@php
    $badgeColor = $statusService->getOrderStatusBadgeColor($order->order_status);
    $statusText = $statusService->getOrderStatusText($order->order_status);
@endphp

<span class="badge bg-{{ $badgeColor }} rounded-pill">
    {{ $statusText }}
</span> 