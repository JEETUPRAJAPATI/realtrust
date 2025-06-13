<?php

namespace App\Events;

use App\Models\Property;
use App\Models\ScheduleProperties;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PropertyVisitScheduled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $property;
    public $user;
    public $visit;
    public $message;
    public $notification_id;


    public function __construct(Property $property, User $user, ScheduleProperties $visit, $notification_id)
    {
        $this->property = $property;
        $this->user = $user;
        $this->visit = $visit;
        $this->notification_id = $notification_id;
        $this->message = "A new user has scheduled a visit for the property with ID: {$this->property->unique_id}. Please review the details and prepare for the upcoming appointment.";
    }

    public function broadcastOn()
    {
        return new Channel('schedule_visit');
    }

    public function broadcastAs()
    {
        return 'property.visit.scheduled';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'property_id' => $this->property->unique_id,
            'user_name' => $this->user->name,
            'notification_id' => $this->notification_id,
            'visit_date' => $this->visit->created_at->toDateTimeString()
        ];
    }
}
