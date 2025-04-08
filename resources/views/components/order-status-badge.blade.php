@props(['order'])

@php
    $badgeColor = match($order->order_status) {
        'delivered' => 'success',
        'cancelled' => 'danger',
        default => 'primary'
    };
@endphp

<span class="badge bg-{{ $badgeColor }}">
    {{ ucfirst($order->order_status) }}
</span> 