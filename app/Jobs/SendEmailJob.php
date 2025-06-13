<?php

namespace App\Jobs;

use App\Mail\PropertyOwnerMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ownerName;
    protected $mailBody;

    protected $timing;

    public function __construct($ownerName, $mailBody,$timing)
    {
        $this->ownerName = $ownerName;
        $this->mailBody = $mailBody;
        $this->timing=$timing;
    }

    public function handle()
    {
        Mail::to('jeetu.radicalloop@gmail.com')->send(new PropertyOwnerMail($this->ownerName,$this->timing ,$this->mailBody));
    }
}
