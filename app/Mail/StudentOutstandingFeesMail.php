<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentOutstandingFeesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $outstandingAmount;
    public $requiredFees;
    public $totalPaid;
    public $dueDate;

    /**
     * Create a new message instance.
     */
    public function __construct(Student $student, float $outstandingAmount, float $requiredFees, float $totalPaid, ?string $dueDate = null)
    {
        $this->student = $student;
        $this->outstandingAmount = $outstandingAmount;
        $this->requiredFees = $requiredFees;
        $this->totalPaid = $totalPaid;
        $this->dueDate = $dueDate ?? now()->addDays(7)->format('Y-m-d');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Outstanding Fees Notice - Report Card Not Available',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student_outstanding_fees',
            with: [
                'student' => $this->student,
                'outstandingAmount' => $this->outstandingAmount,
                'requiredFees' => $this->requiredFees,
                'totalPaid' => $this->totalPaid,
                'dueDate' => $this->dueDate,
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
