<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentCashPaymentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $amountPaid;
    public $balance;
    public $deadline;
    public $totalFees;
    public $paidFees;
    public $registrationFee;
    public $tripFee;
    public $tuitionFee;
    public $practicalFee;
    public $discountAmount;
    public $feeType;

    /**
     * Create a new message instance.
     */
    public function __construct($student, $amountPaid, $balance, $deadline, $totalFees, $paidFees, $registrationFee, $tripFee, $tuitionFee, $practicalFee, $discountAmount, $feeType)
    {
        $this->student = $student;
        $this->amountPaid = $amountPaid;
        $this->balance = $balance;
        $this->deadline = $deadline;
        $this->totalFees = $totalFees;
        $this->paidFees = $paidFees;
        $this->registrationFee = $registrationFee;
        $this->tripFee = $tripFee;
        $this->tuitionFee = $tuitionFee;
        $this->practicalFee = $practicalFee;
        $this->discountAmount = $discountAmount;
        $this->feeType = $feeType;
    }
    public function build()
    {
        return $this->subject('Cash Payment Confirmation')
            ->view('emails.student_cash_payment_confirmation');
    }
}
