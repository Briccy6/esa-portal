<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentDismissalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $dismissalReason;
    public $dismissalNotes;

    /**
     * Create a new message instance.
     */
    public function __construct($student, $dismissalReason, $dismissalNotes = null)
    {
        $this->student = $student;
        $this->dismissalReason = $dismissalReason;
        $this->dismissalNotes = $dismissalNotes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Important Update Regarding Your Status at ESA Education System',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student_dismissal',
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
