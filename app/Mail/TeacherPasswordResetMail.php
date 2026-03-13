<?php

namespace App\Mail;

use App\Models\Teacher;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeacherPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $teacher;
    public $newPassword;

    /**
     * Create a new message instance.
     */
    public function __construct(Teacher $teacher, $newPassword)
    {
        $this->teacher = $teacher;
        $this->newPassword = $newPassword;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Password Has Been Reset')
            ->view('emails.teacher_password_reset');
    }
}
