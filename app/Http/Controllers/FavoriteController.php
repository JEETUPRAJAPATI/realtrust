<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id'
        ]);

        $favorite = Favorite::where('user_id', Auth::id())
            ->where('property_id', $request->property_id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'removed']);
        } else {
            Favorite::create([
                'user_id' => Auth::id(),
                'property_id' => $request->property_id
            ]);
            return response()->json(['status' => 'added']);
        }
    }

    public function checkFavorite($propertyId)
    {
        $isFavorite = Favorite::where('user_id', Auth::id())
            ->where('property_id', $propertyId)
            ->exists();

        return response()->json(['is_favorite' => $isFavorite]);
    }
}
