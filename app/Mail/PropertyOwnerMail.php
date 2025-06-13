<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PropertyOwnerMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $ownerName,$mailBody,$timing;
    public function __construct($ownerName, $mailBody,$timing)
    {
        $this->ownerName = $ownerName;
        $this->mailBody = $mailBody;
        $this->timing = $timing;
    }

    public function build()
    {
        return $this->view('staff.owner.emails.property_owner')
                    ->with([
                        'ownerName' => $this->ownerName,
                        'mailBody' => $this->mailBody,
                        'timing' => $this->timing
                    ]);
    }

}
