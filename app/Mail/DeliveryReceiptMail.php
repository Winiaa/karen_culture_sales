<?php

namespace App\Mail;

use App\Models\Delivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeliveryReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $delivery;

    /**
     * Create a new message instance.
     */
    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Delivery Receipt - Order #' . $this->delivery->order->id)
                    ->markdown('emails.delivery-receipt')
                    ->with([
                        'order' => $this->delivery->order,
                        'delivery' => $this->delivery,
                        'paymentMethod' => $this->delivery->order->payment->payment_method === 'stripe' ? 'Credit Card' : 'Cash on Delivery',
                        'paymentStatus' => $this->delivery->order->payment_status,
                        'deliveryStatus' => $this->delivery->delivery_status,
                        'trackingNumber' => $this->delivery->tracking_number,
                    ]);
    }
} 