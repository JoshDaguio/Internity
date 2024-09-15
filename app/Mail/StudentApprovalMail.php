<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $password;
    public $course;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $email, $password, $course)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->course = $course;
    }

    public function build()
    {
        return $this->subject('Your Account Has Been Approved!')
            ->view('emails.student-approval');
    }
}
