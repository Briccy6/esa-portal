<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Student;

class StudentTranscriptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $transcripts;
    public $totalToBePaid;
    public $totalPaid;
    public $outstanding;
    public $discountAmount;

    public function __construct(Student $student, $transcripts, $totalToBePaid, $totalPaid, $outstanding, $discountAmount)
    {
        $this->student = $student;
        $this->transcripts = $transcripts;
        $this->totalToBePaid = $totalToBePaid;
        $this->totalPaid = $totalPaid;
        $this->outstanding = $outstanding;
        $this->discountAmount = $discountAmount;
    }

    public function build()
    {
        return $this->subject('Your Academic Transcript & Payment History')
            ->view('emails.student_transcript')
            ->with([
                'student' => $this->student,
                'transactions' => $this->transcripts, // If you named it $transcripts in the mailable
                'transcripts' => $this->transcripts,  // For academic transcript section
                'totalToBePaid' => $this->totalToBePaid,
                'totalPaid' => $this->totalPaid,
                'outstanding' => $this->outstanding,
                'discountAmount' => $this->discountAmount,
            ]);
    }
}