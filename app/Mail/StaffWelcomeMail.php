<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $staff;
    public $password;
    public $loginUrl;

    public function __construct($staff, $password, $loginUrl)
    {
        $this->staff = $staff;
        $this->password = $password;
        $this->loginUrl = $loginUrl;
    }

    public function build()
    {
        return $this->subject('Welcome to ESA - Your Staff Account')
            ->view('emails.staff_welcome');
    }
}