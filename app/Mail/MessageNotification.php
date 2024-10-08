<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $messageDetails;

    /**
     * Create a new message instance.
     *
     * @param array $messageDetails
     * @return void
     */
    public function __construct($messageDetails)
    {
        $this->messageDetails = $messageDetails;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->messageDetails['subject'])
                    ->view('emails.message-notification')
                    ->with('messageDetails', $this->messageDetails);
    }
}
