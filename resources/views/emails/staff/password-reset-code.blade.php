<p>Hi {{ $staff->name ?? 'Staff' }},</p>

<p>You requested to reset the password for your ESA Staff account.</p>

<p>Your one-time password reset code is:</p>

<p style="font-size: 24px; font-weight: bold; letter-spacing: 4px;">
    {{ $code }}
</p>

<p>This code will expire in 15 minutes. If you did not request a password reset, you can safely ignore this email.</p>

<p>Best regards,<br>
Excellent Servant Academy Portal</p>

