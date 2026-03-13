<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StudentResetPassword extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset Your ESA Student Password')
            ->greeting('Hello ' . $notifiable->full_name . ',')
            ->line('You requested a password reset for your ESA account.')
            ->action('Reset Password', $url)
            ->line('If you did not request this, please ignore this email.');
    }
}
