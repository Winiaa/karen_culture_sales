@component('mail::message')
# Delivery Receipt

Dear {{ $order->user->name }},

Thank you for your order with Karen Culture Sales. This email serves as your delivery receipt.

## Order Details
**Order Number:** #{{ $order->id }}  
**Order Date:** {{ $order->created_at->format('M d, Y') }}  
**Payment Method:** {{ $paymentMethod }}  
**Payment Status:** {{ $paymentMethod === 'Cash on Delivery' ? 'Completed' : ucfirst($paymentStatus) }}  
**Delivery Status:** {{ ucfirst($deliveryStatus) }}  
**Tracking Number:** {{ $trackingNumber }}

## Order Items
@foreach($order->orderItems as $item)
- {{ $item->product->name }} x {{ $item->quantity }} - @baht($item->subtotal)
@endforeach

## Order Summary
**Subtotal:** @baht($order->subtotal)  
**Shipping:** @if($order->shipping_cost > 0) @baht($order->shipping_cost) @else Free @endif  
**Total:** @baht($order->total_amount)

## Shipping Address
{{ $delivery->recipient_name }}  
{{ $delivery->recipient_phone }}  
{{ $delivery->recipient_address }}

@if($paymentMethod === 'Cash on Delivery')
### Payment Confirmation
Your payment of @baht($order->total_amount) has been received upon delivery.
@endif

@component('mail::button', ['url' => route('orders.show', $order)])
View Order Details
@endcomponent

If you have any questions about your order, please don't hesitate to contact us.

Thanks,<br>
{{ config('app.name') }}
@endcomponent 