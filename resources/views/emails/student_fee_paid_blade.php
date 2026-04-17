{{-- filepath: resources/views/emails/student_fee_paid.blade.php --}}
<p>Dear {{ $student->full_name }},</p>
<p>We have received your payment for <strong>{{ ucfirst($feeType) }}</strong>.</p>
<p>Thank you for your payment. You can now access the relevant services on the portal.</p>
<p>Best regards,<br>Excellent Servant Academy Team</p>