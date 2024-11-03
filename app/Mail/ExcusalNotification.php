<?php

namespace App\Mail;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExcusalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function build()
    {
        return $this->subject("Excusal Notification for Intern " . $this->request->student->profile->full_name)
            ->view('emails.excusal_notification')
            ->with([
                'student' => $this->request->student,
                'reason' => $this->request->reason,
                'penaltyType' => $this->request->penalty_type,
                'attachmentPath' => $this->request->attachment_path,
            ]);
    }
}
