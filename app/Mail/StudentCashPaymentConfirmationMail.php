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
    public $outstandingBalance;
    public $deadline;
    public $totalFees;
    public $paidFees;
    public $registrationFee;
    public $tripFee;
    public $tuitionFee;
    public $practicalFee;
    public $discountAmount;
    public $feeType;
    public $term;
    public $studentCardFee;

    /**
     * Create a new message instance.
     */
    public function __construct($student, $amountPaid, $outstandingBalance, $deadline, $totalFees, $paidFees, $registrationFee, $tripFee, $tuitionFee, $practicalFee, $discountAmount, $feeType, $studentCardFee = 0, $term = null)
    {
        $this->student = $student;
        $this->amountPaid = $amountPaid;
        $this->outstandingBalance = $outstandingBalance;
        $this->deadline = $deadline;
        $this->totalFees = $totalFees;
        $this->paidFees = $paidFees;
        $this->registrationFee = $registrationFee;
        $this->tripFee = $tripFee;
        $this->tuitionFee = $tuitionFee;
        $this->practicalFee = $practicalFee;
        $this->discountAmount = $discountAmount;
        $this->feeType = $feeType;
        $this->studentCardFee = $studentCardFee;
        $this->term = $term;
    }
    
    public function build()
    {
        return $this->subject('Cash Payment Confirmation')
            ->view('emails.student_cash_payment_confirmation');
    }
}

