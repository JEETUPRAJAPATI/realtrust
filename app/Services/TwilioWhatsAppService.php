<?php

// app/Services/TwilioWhatsAppService.php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioWhatsAppService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $this->client = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
        $this->from = env('TWILIO_WHATSAPP_FROM');
    }

    public function sendMessage($to, $message)
    {
        return $this->client->messages->create("whatsapp:{$to}", [
            'from' => $this->from,
            'body' => $message,
        ]);
    }

    public function sendTemplateMessage($to, $templateName, $templateParams)
    {
        return $this->client->messages->create("whatsapp:{$to}", [
            'from' => $this->from,
            'template' => $templateName,
            'template_data' => $templateParams,
        ]);
    }
}
