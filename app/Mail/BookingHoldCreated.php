<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingHoldCreated extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;

    public int $minutesRemaining;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, int $minutesRemaining = 15)
    {
        $this->booking = $booking;
        $this->minutesRemaining = $minutesRemaining;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Suite is Held! - Action Required',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.bookings.hold-created',
            with: [
                'booking' => $this->booking,
                'minutesRemaining' => $this->minutesRemaining,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
