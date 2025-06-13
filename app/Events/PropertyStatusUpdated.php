<?php

namespace App\Events;

use App\Models\Property;
use App\Models\ScheduleProperties;
use App\Models\User;
use Flasher\Prime\Notification\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PropertyStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $property;
    public $message;

    // Constructor to pass property and message
    public function __construct(Property $property, $message)
    {
        $this->property = $property;
        $this->message = $message;
    }

    // Specify the channel to broadcast the event on
    public function broadcastOn()
    {
        return new Channel('notify.' . $this->property->owner->id); // Broadcast to specific owner
    }

    // Specify the event name
    public function broadcastAs()
    {
        return 'PropertyStatusUpdated';
    }

    // Broadcast data
    public function broadcastWith()
    {
        return [
            'property_id' => $this->property->id,
            'status' => $this->property->status,
            'message' => $this->message,
        ];
    }
}
