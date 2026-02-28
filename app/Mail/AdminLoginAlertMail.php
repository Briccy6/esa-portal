<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminLoginAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;
    public $loginTime;
    public $ipAddress;
    public $userAgent;
    public $logoutUrl;

    public function __construct($admin, $loginTime, $ipAddress, $userAgent, $logoutUrl)
    {
        $this->admin = $admin;
        $this->loginTime = $loginTime;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->logoutUrl = $logoutUrl;
    }

    public function build()
    {
        return $this->subject('Admin Login Alert')
            ->view('emails.admin_login_alert');
    }
}