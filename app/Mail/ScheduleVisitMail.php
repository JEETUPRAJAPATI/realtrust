<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScheduleVisitMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user,$field_manager,$timing;
    public function __construct($user, $field_manager,$timing)
    {
        $this->user = $user;
        $this->field_manager = $field_manager;
        $this->timing = $timing;
    }

    public function build()
    {
        return $this->view('staff.schedule_visit.emails.schedule_visit')
                    ->with([
                        'user' => $this->user,
                        'field_manager' => $this->field_manager,
                        'timing' => $this->timing
                    ]);
    }
}
