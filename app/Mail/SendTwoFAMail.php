<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendTwoFAMail extends Mailable
{
    use Queueable, SerializesModels;

    public $secret;

    /**
     * Create a new message instance.
     *
     * @param string $secret
     */
    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.twofa')
            ->subject('Your 2FA Setup for Proviso')
            ->with([
                'secret' => $this->secret,
            ]);
    }
}
