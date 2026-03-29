<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NesaRegistrationStatusMail extends Mailable
{
    use Queueable;

    public $student;
    public $status;
    public $emailContent;
    public $documentPath;

    /**
     * Create a new message instance.
     */
    public function __construct($student, $status, $emailContent, $documentPath = null)
    {
        $this->student = $student;
        $this->status = $status;
        $this->emailContent = $emailContent;
        $this->documentPath = $documentPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->status === 'approved' 
            ? 'NESA Registration Approved - Congratulations!' 
            : 'NESA Registration Update';
            
        return new Envelope(
            subject: $subject
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.nesa_registration_status',
            with: [
                'student' => $this->student,
                'status' => $this->status,
                'emailContent' => $this->emailContent,
                'documentPath' => $this->documentPath,
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
        $attachments = [];
        
        if ($this->documentPath && file_exists($this->documentPath)) {
            $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromPath($this->documentPath)
                ->as('nesa_registration_document.' . pathinfo($this->documentPath, PATHINFO_EXTENSION));
        }
        
        return $attachments;
    }
}
