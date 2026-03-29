<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Timetable;

class TimetableCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $timetable;
    public $student;

    /**
     * Create a new message instance.
     */
    public function __construct(Timetable $timetable, $student)
    {
        $this->timetable = $timetable;
        $this->student = $student;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Timetable Available - ' . $this->timetable->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.timetable-created',
            with: [
                'timetable' => $this->timetable,
                'student' => $this->student,
                'validFrom' => $this->timetable->valid_from ? \Carbon\Carbon::parse($this->timetable->valid_from)->format('M d, Y') : 'Not specified',
                'validUntil' => $this->timetable->valid_until ? \Carbon\Carbon::parse($this->timetable->valid_until)->format('M d, Y') : 'Not specified',
                'hasImage' => !empty($this->timetable->image),
            ]
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
