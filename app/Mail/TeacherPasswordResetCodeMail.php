<?php

namespace App\Mail;

use App\Models\Teacher;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeacherPasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public Teacher $teacher;
    public string $code;

    public function __construct(Teacher $teacher, string $code)
    {
        $this->teacher = $teacher;
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Your ESA Teacher password reset code')
            ->view('emails.teacher.password-reset-code')
            ->with([
                'teacher' => $this->teacher,
                'code'    => $this->code,
            ]);
    }
}

