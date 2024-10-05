<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequirementRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $rejectedType;

    /**
     * Create a new message instance.
     */
    public function __construct($rejectedType)
    {
        $this->rejectedType = $rejectedType;
    }
    
    public function build()
    {
        return $this->subject('Requirement Rejected')
                    ->view('emails.requirements.rejected')
                    ->with([
                        'rejectedType' => $this->rejectedType,
                    ]);
    }
}
