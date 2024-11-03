<?php

namespace App\Mail;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequestStatusNotification extends Mailable
{
    use Queueable, SerializesModels;
    
    public $request;
    public $status;

    public function __construct(Request $request, $status)
    {
        $this->request = $request;
        $this->status = $status;
    }

    public function build()
    {
        return $this->subject("Your Excusal Request Has Been " . ucfirst($this->status))
            ->view('emails.request_status_notification')
            ->with([
                'student' => $this->request->student,
                'status' => $this->status,
                'penaltyType' => $this->request->penalty_type,
                'remarks' => $this->request->admin_remarks,
            ]);
    }
}
