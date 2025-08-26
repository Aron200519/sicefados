<?php

namespace Modules\FABRICASOFT\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\FABRICASOFT\Entities\Preregistration;

class NewPreregistrationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $preregistration;

    /**
     * Create a new message instance.
     */
    public function __construct(Preregistration $preregistration)
    {
        $this->preregistration = $preregistration;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva Solicitud de Desarrollo de Software - FABRICASOFT',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'fabricasoft::emails.new_preregistration',
            with: [
                'preregistration' => $this->preregistration,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
