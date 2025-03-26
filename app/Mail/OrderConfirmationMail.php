<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = 'Order Confirmation - #' . $this->order->id;
        
        return $this->subject($subject)
                    ->markdown('emails.order-confirmation')
                    ->with([
                        'order' => $this->order,
                        'paymentMethod' => $this->order->payment->payment_method === 'stripe' ? 'Credit Card' : 'Cash on Delivery',
                        'paymentStatus' => $this->order->payment_status,
                        'deliveryStatus' => $this->order->delivery ? $this->order->delivery->delivery_status : 'pending',
                        'trackingNumber' => $this->order->delivery ? $this->order->delivery->tracking_number : null,
                    ]);
    }
} 