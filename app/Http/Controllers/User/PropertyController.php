<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Amenities;
use App\Models\ConformTiming;
use App\Models\Feature;
use App\Models\Property;
use App\Models\Payment;
use App\Models\PropertyImageGallery;
use App\Models\ScheduleProperties;
use App\Models\ScheduleVisit;
use Carbon\Carbon;
use App\Models\Society;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class PropertyController extends Controller
{
    protected $now;

    public function __construct()
    {
        $this->now = Carbon::now();
        $this->deleteAllExpiredSchedules();
    }


    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $properties = Property::with('features', 'gallery', 'amenities', 'schedulePropertyTiming', 'society', 'locality', 'city')->where('status', 'Active')->get();
        // dd($properties);
        if ($properties->isEmpty()) {
            return response()->json([
                'message' => 'No properties found.',
                'properties' => []
            ], 404);
        }
        foreach ($properties as $property) {
            $property->floor_plan = $property->flor_image_url; // Use accessor for floor plan URL
            $property->image = $property->image_url; // Use accessor for image URL
        }
        return response()->json([
            'message' => 'properties list retrieved successfully.',
            'properties' => $properties
        ], 200);
    }

    public function show($unique_id)
    {

        $property = Property::where('unique_id', $unique_id)->with('features', 'gallery', 'amenities', 'schedulePropertyTiming', 'society', 'locality', 'city')->where('status', 'Active')->first();

        // $pay = Payment::where('unique_id', $unique_id)->first();
        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        }

        $relatedProperties = Property::with('features', 'gallery', 'amenities', 'schedulePropertyTiming', 'society', 'locality', 'city')->where('status', 'Active')
            ->where('unique_id', '!=', $unique_id) // Exclude the current property
            ->where(function ($query) use ($property) {
                $query->Where('locality', $property->locality) // Match by locality
                    ->orWhere('society_name', $property->society_name); // Match by society
            })
            ->limit(5) // Limit the number of related properties
            ->get();

        // Add additional fields to the related properties
        foreach ($relatedProperties as $relatedProperty) {
            $relatedProperty->floor_plan = $relatedProperty->flor_image_url;
            $relatedProperty->image = $relatedProperty->image_url;
        }

        $property->floor_plan = $property->flor_image_url;
        $property->image = $property->image_url;
        $property->pdf_file_url = $property->pdf_file_url;
        return response()->json([
            'success' => true,
            'data'    => $property,
            'related_properties' => $relatedProperties,
        ], 200);
    }

    public function getForSaleProperties(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $properties = Property::where('purpose', 'sell')
            ->Where('status', 'active')
            ->with('features', 'gallery', 'amenities', 'schedulePropertyTiming', 'society', 'locality', 'city')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        // dd($properties);
        if ($properties->isEmpty()) {
            return response()->json([
                'message' => 'No properties found.',
                'properties' => []
            ], 404);
        }
        foreach ($properties as $property) {
            $property->floor_plan = $property->flor_image_url; // Use accessor for floor plan URL
            $property->image = $property->image_url; // Use accessor for image URL
        }
        return response()->json([
            'message' => 'properties list retrieved successfully.',
            'properties' => $properties
        ], 200);
    }

    // Get properties that are for rent
    public function getForRentProperties(Request $request)
    {
        // dd($request->all());
        $perPage = $request->input('per_page', 10);
        $properties = Property::where('purpose', 'rent')->Where('status', 'active')->with('features', 'gallery', 'amenities', 'schedulePropertyTiming', 'society', 'locality', 'city')->paginate($perPage);
        // dd($properties);
        if ($properties->isEmpty()) {
            return response()->json([
                'message' => 'No properties found.',
                'properties' => []
            ], 404);
        }
        foreach ($properties as $property) {
            $property->floor_plan = $property->flor_image_url; // Use accessor for floor plan URL
            $property->image = $property->image_url; // Use accessor for image URL
        }
        // dd($property);
        return response()->json([
            'message' => 'properties list retrieved successfully.',
            'properties' => $properties
        ], 200);
    }
    public function getForUpCommingProject(Request $request)
    {

        $perPage = $request->input('per_page', 10);
        $properties = Property::where('purpose', 'upcoming_projects')->Where('status', 'active')->with('features', 'video', 'amenities', 'society', 'locality', 'city')->paginate($perPage);
        // dd($properties);
        if ($properties->isEmpty()) {
            return response()->json([
                'message' => 'No properties found.',
                'properties' => []
            ], 404);
        }
        foreach ($properties as $property) {
            $property->image = $property->image_url;
            $property->pdf_file_url = $property->pdf_file_url;
        }
        return response()->json([
            'message' => 'properties list retrieved successfully.',
            'properties' => $properties
        ], 200);
    }
    public function filterProperties(Request $request)
    {

        $query = Property::with('features', 'gallery', 'amenities', 'schedulePropertyTiming', 'society', 'locality', 'city')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc'); // Only active properties

        // Filter by furnish_type
        if ($request->has('furnish_type')) {
            $furnishTypes = array_map('trim', explode(',', $request->input('furnish_type')));
            $query->whereIn('furnish_type', $furnishTypes);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Price filters
        if ($request->has('price')) {
            $priceRange = explode('-', $request->input('price'));
            if (count($priceRange) == 2) {
                $query->whereBetween('price', [$priceRange[0], $priceRange[1]]);
            }
        }

        // Filter by purpose
        if ($request->filled('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        // Filter by locality (single value)
        if ($request->filled('locality')) {
            $localities = array_map('trim', explode(',', $request->input('locality')));
            $query->whereIn('locality', $localities);
        }

        // Filter by society (comma-separated values)
        if ($request->filled('society')) {
            $societies = array_map('trim', explode(',', $request->input('society')));
            $query->whereIn('society_name', $societies);
        }

        // Filter by BHK (comma-separated values)
        if ($request->filled('bhk')) {
            $bhkValues = array_map('trim', explode(',', $request->input('bhk')));
            $query->whereIn('bhk', $bhkValues);
        }

        $properties = $query->get();

        // Sort: properties with schedulePropertyTiming first
        $properties = $properties->sortByDesc(function ($property) {
            return $property->schedulePropertyTiming !== null;
        })->values(); // Reset keys after sort



        // Add additional fields to each property
        foreach ($properties as $property) {
            $property->floor_plan = $property->flor_image_url;
            $property->image = $property->image_url;
        }

        if ($properties->isEmpty()) {
            return response()->json([
                'message' => 'No properties found.',
                'properties' => []
            ], 404);
        }

        return response()->json([
            'message' => 'Properties list retrieved successfully.',
            'properties' => [
                'data' => $properties
            ]
        ], 200);
    }


    public function properties_list(Request $request)
    {
        // dd($request->all());
        try {
            // Retrieve and sanitize the filters
            $statusFilter = $request->input('status_filter');
            $limitFrom = $request->input('limit_from');
            $sortBy = $request->input('sort_by_dd');
            $propertyTypeFilter = $request->input('proptype_filter');
            $purposeFilter = $request->input('purpose_filter');
            $availableFilter = $request->input('available_filter');
            $minPrice = $request->input('budget_min_price_filter');
            $maxPrice = $request->input('budget_max_price_filter');
            $searchById = $request->input('search_by_id_filter');
            $perPage = $request->input('per_page', 10);
            // Start Eloquent Query
            $query = Property::with('features', 'gallery', 'amenities', 'schedulePropertyTiming', 'society', 'locality', 'city');

            // Apply filters
            if (!empty($statusFilter)) {
                $query->where('status', $statusFilter);
            }

            if (!empty($propertyTypeFilter)) {
                $query->where('type', $propertyTypeFilter);
            }

            if (!empty($purposeFilter)) {
                $query->where('purpose', $purposeFilter);
            }

            if ($availableFilter !== 'All') {
                $query->where('available_for', $availableFilter);
            }

            if (!empty($minPrice)) {
                $query->where('price', '>=', $minPrice);
            }

            if (!empty($maxPrice)) {
                $query->where('price', '<=', $maxPrice);
            }

            if (!empty($searchById)) {
                $query->where('unique_id', $searchById);
            }
            // Apply Sorting
            switch ($sortBy) {
                case 'date_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'date_desc':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
            $properties = $query->skip($limitFrom)->paginate($perPage);
            foreach ($properties as $property) {
                $property->floor_plan = $property->flor_image_url;
                $property->image = $property->image_url;
            }
            return response()->json([
                'success' => true,
                'properties' => $properties,
                'properties_count' => $properties->total()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the properties.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function galaryImages()
    {
        try {
            $properties = Property::with('gallery')->where('status', 'Active')->has('gallery')->get();
            foreach ($properties as $property) {
                $property->floor_plan = $property->floor_plan_url;
                $property->image = $property->image_url;
            }

            return response()->json([
                'status' => 'success',
                'data' => $properties
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch property images. Please try again later.'
            ], 500);
        }
    }
    public function getFeatures()
    {
        try {
            $features = Feature::get();
            // dd( $features);
            foreach ($features as $property) {
                $property->image = $property->image_url;
            }
            return response()->json([
                'status' => 'success',
                'data' => $features
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch features images. Please try again later.'
            ], 500);
        }
    }
    public function getAmenities()
    {
        try {
            $amenities = Amenities::get();
            foreach ($amenities as $property) {
                $property->image = $property->image_url;
            }
            return response()->json([
                'status' => 'success',
                'data' => $amenities
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch amenities. Please try again later.'
            ], 500);
        }
    }

    public function filterItem(Request $request)
    {

        $query = Property::with('features', 'gallery', 'amenities', 'schedulePropertyTiming', 'society', 'locality', 'city')
            ->where('status', 'active'); // Only active properties

        // Filter by furnish type
        if ($request->filled('furnish_type')) {
            $furnishType = $request->input('furnish_type');
            if (is_array($furnishType)) {
                $query->whereIn('furnish_type', $furnishType);
            } else {
                $query->where('furnish_type', $furnishType);
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Price filters
        if ($request->has('price')) {
            $priceRange = explode('-', $request->input('price'));
            if (count($priceRange) == 2) {
                $query->whereBetween('price', [$priceRange[0], $priceRange[1]]);
            }
        }

        // Filter by purpose
        if ($request->filled('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        // Filter by society (check if data is available)
        if ($request->filled('society') && is_array($request->society)) {
            $query->where(function ($q) use ($request) {
                foreach ($request->society as $society) {
                    $q->orWhere('society_name', 'LIKE', '%' . $society . '%');
                }
            });
        }

        // Filter by BHK
        if ($request->filled('bhk') && is_array($request->bhk)) {
            $query->whereIn('bhk', $request->bhk);
        }

        // Filter by locality
        if ($request->filled('locality')) {
            $query->where('locality', $request->locality);
        }

        $properties = $query->get(); // Get the filtered properties

        // Process properties for the response
        foreach ($properties as $property) {
            $property->floor_plan = $property->flor_image_url;
            $property->image = $property->image_url;
        }

        // Return response
        if ($properties->isEmpty()) {
            return response()->json([
                'message' => 'No properties found.',
                'properties' => []
            ], 404);
        }

        return response()->json([
            'message' => 'Properties list retrieved successfully.',
            'properties' => $properties
        ], 200);
    }

    public function shareUrl(Request $request)
    {

        // dd($request->all());
        $relocation = $request->query('relocation');
        $propertyId = $request->query('property_id');
        Log::info('Url calling property_id ================================', [$relocation, $propertyId]);
        if (!$relocation || !$propertyId) {
            return response('Bad request: Missing relocation URL or property ID', 400);
        }

        $isCrawler = function ($userAgent) {
            $crawlers = [
                'WhatsApp',
                'facebookexternalhit',
                'Twitterbot',
                'Slackbot',
                'LinkedInBot',
            ];
            foreach ($crawlers as $crawler) {
                if (stripos($userAgent, $crawler) !== false) {
                    return true;
                }
            }
            return false;
        };

        $userAgent = $request->header('User-Agent');
        Log::info('userAgent', [$userAgent]);
        if ($isCrawler($userAgent)) {
            try {

                $property = Property::where('unique_id', $propertyId)->with('features', 'gallery', 'amenities', 'schedulePropertyTiming', 'society', 'localities')->where('status', 'Active')->first();
                // dd($property);
                if (empty($property)) {
                    return response('Property not found', 404);
                }
                $property->image = $property->image_url;
                // dd($property->image);
                $shortDescription = Str::limit($property->description, 300);
                $imageUrl = url($property->image_url); // Ensuring it's an absolute URL

                return response()->make("
                    <!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <meta property='og:title' content='" . e($property->title) . "'>
                        <meta property='og:description' content='" . e($shortDescription) . "'>
                        <meta property='og:image' content='" . e($imageUrl) . "'>
                        <meta property='og:url' content='" . e($relocation) . "'>
                        <meta property='og:type' content='website'>
                        <title>" . e($property->title) . "</title>
                    </head>
                    <body>
                        Redirecting...
                        <script>
                            window.location.href = '" . e($relocation) . "';
                        </script>
                    </body>
                    </html>
                ", 200, ['Content-Type' => 'text/html']);
            } catch (\Exception $e) {
                Log::error("Error fetching property: " . $e->getMessage());
                return response('Server error', 500);
            }
        } else {
            return redirect($relocation);
        }
    }

    public function property_list()
    {
        try {
            $owner = Auth::guard('owner')->user();

            if (!$owner) {
                return response()->json(['error' => 'Owner not authenticated'], 401);
            }
            $properties = Property::where('status', 'Active')
                ->select('unique_id', 'title')->where('owner_id', $owner->id)
                ->get()->toArray();
            return response()->json([
                'status' => 'success',
                'data' => $properties
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch property images. Please try again later.'
            ], 500);
        }
    }

    public function deleteAllExpiredSchedules()
    {
        try {
            $visits = ScheduleVisit::all();
            $expiredPropertyIds = [];

            foreach ($visits as $visit) {
                if (!$visit->timing) continue;

                $parts = explode(' - ', $visit->timing);
                if (count($parts) !== 2) continue;

                try {
                    $endTime = Carbon::createFromFormat('m/d/Y h:i A', trim($parts[1]));

                    if ($endTime->lt($this->now)) {
                        $expiredPropertyIds[] = $visit->property_id;
                    }
                } catch (\Exception $e) {
                    Log::error("Invalid timing format in visit ID {$visit->id}: {$visit->timing}");
                }
            }

            $expiredPropertyIds = array_unique($expiredPropertyIds);

            foreach ($expiredPropertyIds as $propertyId) {
                ScheduleVisit::where('property_id', $propertyId)->delete();
                ScheduleProperties::where('property_id', $propertyId)->delete();

                $conformTimings = ConformTiming::where('property_id', $propertyId)->get();

                foreach ($conformTimings as $timing) {
                    $parts = explode(' - ', $timing->timing);
                    if (count($parts) !== 2) continue;

                    try {
                        $endTime = Carbon::createFromFormat('m/d/Y h:i A', trim($parts[1]));
                        if ($endTime->lt($this->now)) {
                            $timing->delete();
                        }
                    } catch (\Exception $e) {
                        Log::error("Invalid conform_timing format for ID {$timing->id}: {$timing->timing}");
                    }
                }
            }

            // Optional: log or show response
            Log::info('Expired schedule cleanup completed.');
        } catch (\Exception $e) {
            Log::error('Failed to delete expired schedules: ' . $e->getMessage());
        }
    }
}
