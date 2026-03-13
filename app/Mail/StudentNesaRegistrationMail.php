<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class StudentNesaRegistrationMail extends Mailable
{
    use Queueable;

    public $studentData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $studentData)
    {
        $this->studentData = $studentData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'NESA Registration Confirmation - National Examination Ready'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student_nesa_registration',
            with: [
                'studentData' => $this->studentData,
            ],
        );
    }
}
