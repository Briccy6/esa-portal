@component('mail::message')
# Report Card Not Available

Dear {{ $student->full_name }},

We hope this message finds you well. We are writing to inform you that your **report card cannot be generated** at this time due to **outstanding fees** on your account.

## Current Fee Status

| Item | Amount |
|------|--------|
| **Total Required Fees** | {{ config('app.currency_symbol', 'RWF') }} {{ number_format($requiredFees, 2) }} |
| **Amount Paid** | {{ config('app.currency_symbol', 'RWF') }} {{ number_format($totalPaid, 2) }} |
| **Outstanding Balance** | **{{ config('app.currency_symbol', 'RWF') }} {{ number_format($outstandingAmount, 2) }}** |

## Important Notice

To access your report card, you must **settle all outstanding fees** on or before **{{ date('F d, Y', strtotime($dueDate)) }}**.

Your report card will be **automatically generated** as soon as your payment is confirmed in our system. This typically happens within 24 hours of payment.

## Payment Information

Please contact our Finance Department for available payment methods and arrangements:

- **Email**: finance@excellentservantacademy.ac.rw
- **Phone**: [INSERT PHONE NUMBER]
- **Office Hours**: Monday - Friday, 8:00 AM - 4:00 PM

## What Happens Next

Once you have made your payment:
1. Your payment will be verified by our finance team
2. Your report card will be generated automatically
3. You will receive an email notification with your report card
4. You can download and print your report card from the student portal

---

**Note**: This is an automated message from our school management system. Please do not reply to this email. For inquiries, contact the Finance Department directly.

Thank you for your prompt attention to this matter.

Best regards,  
**Excellent Servant Academy**

*This email contains important information about your academic record. Please save for your records.*
@endcomponent
