<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FacultyUpdateNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $updatedFields;
    public $newPassword;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $email, $updatedFields, $newPassword = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->updatedFields = $updatedFields;
        $this->newPassword = $newPassword; // Pass the new password
    }
        /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Faculty Account Information Has Been Updated')
                    ->view('emails.faculty-update-notification');
    }
}
