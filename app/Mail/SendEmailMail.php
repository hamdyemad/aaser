<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;


class SendEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $customMessage;
    public $link;
    public $image;
    public $file;
    public $email;

    public function __construct(public $subject,$user,$customMessage,$link,$image,$file,$email)
    {
        $this->user = $user;
        $this->customMessage = $customMessage;
        $this->link = $link;
        $this->image = $image;
        $this->file = $file;
        $this->email = $email;
    }

    public function build()
    {
        $email = $this->email;
        $mailer = $this->subject($this->subject)->view('emails.message')->with([
            'user' => $this->user,
            'link' => $this->link,
            'image' => $this->image,
            'file' => $this->file,
            'email' => $this->email,
            'customMessage' => $this->customMessage,
        ]);
        return $mailer;
    }

    // public function attachments(): array
    // {
    //     return [
    //         Attachment::fromPath(asset('storage/' . $this->image)),
    //     ];
    // }

}
