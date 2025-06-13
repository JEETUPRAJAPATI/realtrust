<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class StaffNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $ownerId;
    public $propertyId;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($ownerId, $propertyId)
    {
        $this->ownerId = $ownerId;
        $this->propertyId = $propertyId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast']; // Using database and pusher broadcasting
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */

    public function toDatabase($notifiable)
    {
        return [
            'property_id' => $this->propertyId,
            'owner_id' => $this->ownerId,
            'message' => "A new property has been added by Owner ID: {$this->ownerId}, Property ID: {$this->propertyId}",
        ];
    }
    public function toArray($notifiable)
    {
        return [
            'message' => "A new property has been added by Owner ID: {$this->ownerId}, Property ID: {$this->propertyId}",
            'owner_id' => $this->ownerId,
            'property_id' => $this->propertyId
        ];
    }

    /**
     * Get the broadcastable message representation.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "A new property has been added by Owner ID: {$this->ownerId}, Property ID: {$this->propertyId}",
            'owner_id' => $this->ownerId,
            'property_id' => $this->propertyId
        ]);
    }
}
