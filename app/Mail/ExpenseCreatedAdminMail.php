<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExpenseCreatedAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $expense;

    public function __construct($expense)
    {
        $this->expense = $expense;
    }

    public function build()
    {
        return $this->subject('New Expense Recorded')
            ->view('emails.expense_created_admin');
    }
}