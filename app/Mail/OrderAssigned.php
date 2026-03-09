<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public User $assignedUser,
        public User $assignedBy
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Order Assigned to You - ' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-assigned',
        );
    }
}
