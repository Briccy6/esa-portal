<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentCardGeneratedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $studentData;

    public function __construct($studentData)
    {
        $this->studentData = $studentData;
    }

    public function build()
    {
        return $this->subject('Your ESA Student ID Card is Ready for Collection!')
                    ->view('emails.student_card_generated')
                    ->with('studentData', $this->studentData);
    }
}
