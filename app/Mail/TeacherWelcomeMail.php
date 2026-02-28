<?php

namespace App\Mail;

use App\Models\Teacher;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeacherWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $teacher;
    public $plainPassword;
    public $courses;

    /**
     * Create a new message instance.
     */
    public function __construct(Teacher $teacher, $plainPassword, $courses = [])
    {
        $this->teacher = $teacher;
        $this->plainPassword = $plainPassword;
        $this->courses = $courses;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Welcome to ESA - Your Teacher Account')
            ->view('emails.teacher_welcome');
    }
}
