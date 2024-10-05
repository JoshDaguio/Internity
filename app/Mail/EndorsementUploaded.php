<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EndorsementUploaded extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('Endorsement Letter Uploaded')
                    ->view('emails.requirements.endorsement');
    }
}
