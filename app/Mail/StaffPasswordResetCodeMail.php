<?php

namespace App\Mail;

use App\Models\Staff;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffPasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public Staff $staff;
    public string $code;

    public function __construct(Staff $staff, string $code)
    {
        $this->staff = $staff;
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Your ESA Staff password reset code')
            ->view('emails.staff.password-reset-code')
            ->with([
                'staff' => $this->staff,
                'code'  => $this->code,
            ]);
    }
}

