<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Exam;
use Illuminate\Support\Collection;

class ExamTeacherMail extends Mailable
{
    use Queueable, SerializesModels;

    public $exam;
    public $lessons;

    /**
     * Create a new message instance.
     */
    public function __construct(Exam $exam, Collection $lessons)
    {
        $this->exam = $exam;
        $this->lessons = $lessons;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Exam Teacher Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
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

    public function build()
    {
        return $this->subject('Action Required: Add Questions for Exam "' . $this->exam->title . '"')
            ->view('emails.exam_teacher');
    }
}
