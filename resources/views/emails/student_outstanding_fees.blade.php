@component('mail::message')

<h2>Report Card Not Available</h2>

<p>Dear <strong>{{ $student->full_name }}</strong>,</p>

<p>We hope this message finds you well. We are writing to inform you that your <strong>report card cannot be generated</strong> at this time due to <strong>outstanding fees</strong> on your account.</p>

<h3>Current Fee Status</h3>

<table class="fee-table">
    <thead>
        <tr>
            <th>Item</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Total Required Fees</strong></td>
            <td>RWF {{ number_format($requiredFees, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Amount Paid</strong></td>
            <td>RWF {{ number_format($totalPaid, 2) }}</td>
        </tr>
        <tr class="total">
            <td><strong>Outstanding Balance</strong></td>
            <td><strong>RWF {{ number_format($outstandingAmount, 2) }}</strong></td>
        </tr>
    </tbody>
</table>

<div class="alert-box">
    <strong>⚠️ Important Notice</strong><br><br>
    To access your report card, you must <strong>settle all outstanding fees</strong> on or before <strong>{{ date('F d, Y', strtotime($dueDate ?? now())) }}</strong>.
</div>

<p>Your report card will be <strong>automatically generated</strong> as soon as your payment is confirmed in our system. This typically happens within 24 hours of payment.</p>

<h3>Payment Information</h3>

<div class="contact-info">
    <strong>📞 Finance Department Contact Details:</strong><br><br>
    <strong>Email:</strong> <a href="mailto:finance@excellentservantacademy.com">finance@excellentservantacademy.com</a><br>
    <strong>Phone:</strong> 0788525247 <br>
    <strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 4:00 PM
</div>

<h3>What Happens Next</h3>

<p>Once you have made your payment, please follow these steps:</p>

<ol>
    <li>Your payment will be verified by our finance team</li>
    <li>Your report card will be generated automatically</li>
    <li>You will receive an email notification with your report card</li>
    <li>You can download and print your report card from the student portal</li>
</ol>

<div class="highlight-box">
    <strong>📌 Note:</strong> This is an automated message from our school management system. Please do <strong>not</strong> reply to this email. For inquiries, contact the Finance Department directly.
</div>

<p style="margin-top: 30px; color: #666; font-size: 14px;">
    Thank you for your prompt attention to this matter.<br><br>
    <strong>Best regards,</strong><br>
    <strong>Excellent Servant Academy</strong>
</p>

<p style="margin-top: 20px; color: #999; font-size: 12px; border-top: 1px solid #e5e7eb; padding-top: 15px;">
    <em>This email contains important information about your academic record. Please save for your records.</em>
</p>

@endcomponent
