<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $reset;

    public function __construct($user,$reset)
    {
        $this->user = $user;
        $this->reset = $reset;
    }

    public function build()
    {
        return $this->subject('Hona Asseer Support')->view('emails.reset');
    }
}
