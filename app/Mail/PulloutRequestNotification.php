<?php

namespace App\Mail;

use App\Models\Pullout;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PulloutRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $pullout;

    public function __construct(Pullout $pullout)
    {
        $this->pullout = $pullout;
    }

    public function build()
    {
        return $this->view('emails.pullout_request_notification')
            ->subject('Pullout Request Notification');
    }
}
