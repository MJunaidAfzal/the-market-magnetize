<?php

namespace App\Mail;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public User $assignedUser,
        public User $assignedBy
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Lead Assigned to You - ' . $this->lead->full_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lead-assigned',
        );
    }
}
