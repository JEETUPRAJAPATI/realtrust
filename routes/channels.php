<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('property-added', function ($user, $ownerId) {
    return true; // Modify according to your authorization needs
});

Broadcast::channel('schedule_visit', function () {
    return true; // Adjust this based on your authorization requirements.
});
Broadcast::channel('location-tracking.{fieldManagerId}', function ($user, $fieldManagerId) {
    // Return true if the user is authorized to listen to this channel
    return true; // Add your own authorization logic here
});

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
