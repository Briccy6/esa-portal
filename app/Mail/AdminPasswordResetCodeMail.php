<?php

namespace App\Mail;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public Admin $admin;
    public string $code;

    public function __construct(Admin $admin, string $code)
    {
        $this->admin = $admin;
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Your ESA Admin password reset code')
            ->view('emails.admin.password-reset-code')
            ->with([
                'admin' => $this->admin,
                'code'  => $this->code,
            ]);
    }
}

