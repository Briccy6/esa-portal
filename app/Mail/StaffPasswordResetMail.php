<?php

namespace App\Mail;

use App\Models\Staff;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $staff;
    public $newPassword;

    /**
     * Create a new message instance.
     */
    public function __construct(Staff $staff, $newPassword)
    {
        $this->staff = $staff;
        $this->newPassword = $newPassword;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Password Has Been Reset')
            ->view('emails.staff_password_reset');
    }
}
