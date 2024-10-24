<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EvaluationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $evaluation;

    public function __construct($evaluation)
    {
        $this->evaluation = $evaluation;
    }

    public function build()
    {
        return $this->subject('New Evaluation Available')
                    ->view('emails.evaluation_notification')
                    ->with('evaluation', $this->evaluation);
    }
}
