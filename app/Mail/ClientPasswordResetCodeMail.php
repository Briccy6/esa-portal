<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientPasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public Client $client;
    public string $code;

    public function __construct(Client $client, string $code)
    {
        $this->client = $client;
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Your ESA Portal password reset code')
            ->view('emails.client.password-reset-code')
            ->with([
                'client' => $this->client,
                'code'   => $this->code,
            ]);
    }
}

