<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $fieldManagerId;
    public $latitude;
    public $longitude;

    public function __construct($fieldManagerId, $latitude, $longitude)
    {
        $this->fieldManagerId = $fieldManagerId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        Log::info("LocationUpdated event created for Field Manager ID: $fieldManagerId with Latitude: $latitude and Longitude: $longitude");

    }

    public function broadcastOn()
    {
        Log::info("CALLIGN");
        return new Channel('location-tracking.' . $this->fieldManagerId);
    }


    public function broadcastWith()
    {
        Log::info('Broadcasting LocationUpdated event', [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'fieldManagerId' => $this->fieldManagerId
        ]);

        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

}
