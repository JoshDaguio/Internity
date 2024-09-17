<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InterviewScheduled extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $interview;

    /**
     * Create a new message instance.
     */
    public function __construct($application, $interview)
    {
        $this->application = $application;
        $this->interview = $interview;
    }

    public function build()
    {
        return $this->view('emails.interview-scheduled')
                    ->subject('Interview Scheduled')
                    ->with([
                        'application' => $this->application,
                        'interview' => $this->interview,
                    ]);
    }

}
