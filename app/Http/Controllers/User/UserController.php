<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Cities;
use App\Models\Locality;
use App\Models\Post;
use App\Models\Property;
use App\Models\Slider;

use App\Models\AdditionalDetail;
use App\Models\Society;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function sliders()
    {

        $sliders = Slider::latest()->get();
        if ($sliders->isEmpty()) {
            return response()->json([
                'message' => 'No sliders found.',
                'sliders' => []
            ], 404);
        }
        return response()->json([
            'message' => 'Sliders list retrieved successfully.',
            'sliders' => $sliders
        ], 200);
    }
    public function posts()
    {
        $posts = Post::latest()->with(['staff', 'admin', 'comments'])->get();
        if ($posts->isEmpty()) {
            return response()->json([
                'message' => 'No posts found.',
                'posts' => []
            ], 404);
        }
        return response()->json([
            'message' => 'posts list retrieved successfully.',
            'posts' => PostResource::collection($posts),
        ], 200);
    }
    public function locality()
    {
        $locality = Locality::all();
        if ($locality->isEmpty()) {
            return response()->json([
                'message' => 'No locality found.',
                'posts' => []
            ], 404);
        }
        return response()->json([
            'message' => 'locality retrieved successfully.',
            'locality' => $locality,
        ], 200);
    }
    public function socity(Request $request)
    {
        if ($request->locality_id) {
            $socity = Society::where('locality_id', $request->locality_id)->with('locality')->get();
        } else {
            $socity = Society::with('locality')->get();
        }

        if ($socity->isEmpty()) {
            return response()->json([
                'message' => 'No society found.',
                'posts' => []
            ], 404);
        }

        return response()->json([
            'message' => 'Society retrieved successfully.',
            'socity' => $socity,
        ], 200);
    }

    
     public function additionalDetails()
    {
        try {
            $additionalDetails = AdditionalDetail::all();

            // Check if data exists
            if ($additionalDetails->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No additional details found.',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Additional details retrieved successfully.',
                'data' => $additionalDetails
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
