<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Teacher;

class TeacherWelcomeBackMail extends Mailable
{
    use Queueable, SerializesModels;

    public $teacher;
    public $loginTime;
    public $ipAddress;
    public $userAgent;
    public $logoutUrl;

    public function __construct(Teacher $teacher, $loginTime, $ipAddress, $userAgent, $logoutUrl)
    {
        $this->teacher = $teacher;
        $this->loginTime = $loginTime;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->logoutUrl = $logoutUrl;
    }

    public function build()
    {
        return $this->subject('We have detected th login with your acount')
            ->view('emails.teacher_welcome_back');
    }
}
