<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentAccepted extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $start_date;
    public $schedule;
    public $work_type;
    public $start_time;
    public $end_time;

    /**
     * Create a new message instance.
     */
    public function __construct($application, $start_date, $schedule, $work_type, $start_time, $end_time)
    {
        $this->application = $application;
        $this->start_date = $start_date;
        $this->schedule = $schedule;
        $this->work_type = $work_type;
        $this->start_time = $start_time; // Pass start time directly
        $this->end_time = $end_time;     // Pass end time directly
    }

    public function build()
    {
        return $this->view('emails.student-accepted')
                    ->subject('You have been accepted as an intern')
                    ->with([
                        'start_date' => $this->start_date,
                        'schedule' => $this->schedule,
                        'work_type' => $this->work_type,
                        'start_time' => $this->start_time,
                        'end_time' => $this->end_time,
                    ]);
    }
}
