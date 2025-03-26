@component('mail::message')
# Order Confirmation

Dear {{ $order->user->name }},

Thank you for your order with Karen Culture Sales. We're excited to confirm that your order has been received and is being processed.

## Order Details
**Order Number:** #{{ $order->id }}  
**Order Date:** {{ $order->created_at->format('M d, Y') }}  
**Payment Method:** {{ $paymentMethod }}  
**Payment Status:** {{ ucfirst($paymentStatus) }}  
**Delivery Status:** {{ ucfirst($deliveryStatus) }}

@if($trackingNumber)
**Tracking Number:** {{ $trackingNumber }}
@endif

## Order Items
@foreach($order->orderItems as $item)
- {{ $item->product->name }} x {{ $item->quantity }} - @baht($item->price * $item->quantity)
@endforeach

## Order Summary
**Subtotal:** @baht($order->subtotal)  
**Shipping:** @baht($order->shipping_fee)  
**Total:** @baht($order->total_amount)

## Shipping Address
{{ $order->delivery->recipient_name }}  
{{ $order->delivery->recipient_phone }}  
{{ $order->delivery->recipient_address }}

@if($paymentMethod === 'Cash on Delivery')
### Cash on Delivery Instructions
Your order will be delivered to the address above. Please have the exact amount ready for payment upon delivery.

**Amount to Pay:** @baht($order->total_amount)
@endif

@component('mail::button', ['url' => route('orders.show', $order)])
View Order Details
@endcomponent

If you have any questions about your order, please don't hesitate to contact us.

Thanks,<br>
{{ config('app.name') }}
@endcomponent 