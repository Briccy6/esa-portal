<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminLoginAlert extends Notification
{
    use Queueable;

    public $ip;
    public $time;
    public $userAgent;

    /**
     * Create a new notification instance.
     */
    public function __construct($ip, $time, $userAgent)
    {
        $this->ip = $ip;
        $this->time = $time;
        $this->userAgent = $userAgent;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $logoutUrl = url('/admin/force-logout?token=' . csrf_token());

        return (new MailMessage)
            ->subject('New Admin Login Detected')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A login to your admin account was detected.')
            ->line('**Time:** ' . $this->time)
            ->line('**IP Address:** ' . $this->ip)
            ->line('**Device:** ' . $this->userAgent)
            ->action('It\'s not me (Logout)', $logoutUrl)
            ->line('If this was not you, click the button above to log out your account immediately.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
