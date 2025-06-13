<?php

namespace App\Http\Controllers\FieldManager;

use App\Events\LocationUpdated;
use App\Http\Controllers\Controller;
use App\Models\FieldManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function updateLocation(Request $request, $id)
    {
        $fieldManager = FieldManager::findOrFail($id);
        $fieldManager->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
        event(new LocationUpdated($fieldManager->id, $request->latitude, $request->longitude));
        Log::info('LocationUpdated event triggered for Field Manager ID: ' . $fieldManager->id);

        return response()->json(['status' => 'Location updated successfully']);
    }
}
