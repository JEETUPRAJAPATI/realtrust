<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StaffNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ownerId;
    public $propertyId;
    public $message;
    public $notification_id;
    /**
     * Create a new event instance.
     *
     * @param int $ownerId
     * @param string $propertyId
     * @return void
     */
    public function __construct($ownerId, $propertyId, $notification_id)
    {
        $this->ownerId = $ownerId;
        $this->propertyId = $propertyId;
        $this->notification_id = $notification_id;
        $this->message = "A new property has been added with Property ID: {$this->propertyId}";
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('property-added');
    }
    public function broadcastAs()
    {
        return 'message.sent';
    }
    /**
     * Get the broadcastable data.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => uniqid(),
            'message' => $this->message,
            'owner_id' => $this->ownerId,
            'notification_id' => $this->notification_id,
            'property_id' => $this->propertyId
        ];
    }
}
