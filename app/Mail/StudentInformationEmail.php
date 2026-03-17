<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Student;

class StudentInformationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $password;

    /**
     * Create a new message instance.
     *
     * @param Student $student
     * @param string $password
     * @return void
     */
    public function __construct(Student $student, $password = null)
    {
        $this->student = $student;
        $this->password = $password ?? $student->password_plain ?? 'Your chosen password';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Student Information - ESA Portal')
                    ->view('emails.student_information')
                    ->with([
                        'student' => $this->student,
                        'password' => $this->password,
                    ]);
    }
}
