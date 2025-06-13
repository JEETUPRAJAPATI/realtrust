<?php

namespace App\Notifications;


use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PropertyStatusUpdatedNotification extends Notification
{
    public $property;
    public $message;

    // Constructor to pass the property and message
    public function __construct(Property $property, $message)
    {
        $this->property = $property;
        $this->message = $message;
    }

    // Define the delivery channels (database and broadcast)
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    // Notification data to be stored in the database
    public function toDatabase($notifiable)
    {
        return [
            'property_id' => $this->property->id,
            'status' => $this->property->status,
            'message' => $this->message,
        ];
    }

    // Broadcast the notification data
    public function toBroadcast($notifiable)
    {
        return [
            'property_id' => $this->property->id,
            'status' => $this->property->status,
            'message' => $this->message,
        ];
    }
}
