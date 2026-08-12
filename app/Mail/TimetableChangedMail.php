<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Timetable;

class TimetableChangedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $timetable;
    public $student;
    public $summary;
    public $details;

    public function __construct(Timetable $timetable, $student, string $summary, array $details = [])
    {
        $this->timetable = $timetable;
        $this->student = $student;
        $this->summary = $summary;
        $this->details = $details;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Timetable Update: ' . $this->summary,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.timetable_changed',
            with: [
                'timetable' => $this->timetable,
                'student' => $this->student,
                'summary' => $this->summary,
                'details' => $this->details,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
