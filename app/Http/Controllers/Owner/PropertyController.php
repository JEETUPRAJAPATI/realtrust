<?php

namespace App\Http\Controllers\Owner;

use App\Events\StaffNotification as EventsStaffNotification;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Staff;
use App\Models\UserInterest;
use App\Notifications\StaffNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use App\Models\Society;

class PropertyController extends Controller
{


    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $properties = Property::latest()->with('features', 'gallery', 'amenities')->paginate($perPage);
        if ($properties->isEmpty()) {
            return response()->json([
                'message' => 'No properties found.',
                'properties' => []
            ], 404);
        }
        return response()->json([
            'message' => 'properties list retrieved successfully.',
            'properties' => $properties
        ], 200);
    }

    public function show($unique_id)
    {
        $property = Property::where('unique_id', $unique_id)->with('features', 'gallery', 'amenities', 'society', 'locality', 'city')->first();

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        }
        $property->floor_plan = $property->flor_image_url;
        $property->image = $property->image_url;
        return response()->json([
            'success' => true,
            'data'    => $property
        ], 200);
    }

    public function store(Request $request)
    {
        // dd($_FILES);
        // Validate request
        $validator = Validator::make($request->all(), [
            'price'     => 'nullable|numeric',
            'deposit' => 'nullable|numeric',
            'monthly_rent' => 'nullable|numeric',
            'maintenance' => 'nullable|numeric',
            'purpose'   => 'required',
            'type'      => 'required',
            'bedroom'   => 'required|integer|min:1',
            'bathroom'  => 'required|integer|min:1',
            'bhk'  => 'required|integer|min:1',
            'features' => 'required|string',
            'features.*' => 'string|exists:features,id',
            'amenities' => 'required|string',
            'amenities.*' => 'string|exists:amenities,id',
            'furnish_type' => 'required',
            'city'      => 'required|max:100',
            'locality'  => 'required|max:100',
            'area'      => 'required|numeric',
            'floor_plan' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'gallaryimage.*' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'description' => 'required',
            'additional_detail' => 'required',
            // 'latitude' => 'required|numeric',
            // 'longitude' => 'required|numeric',
            'age' => 'required|integer|min:1|max:120',
            'available_for' => 'required|date',
            'floor'  => 'required|integer|min:1',
            'block_no' => 'required',
            'flat_no' => 'required',
        ]);
        $society = Society::where('id', $request->input('society_name'))->first();
        $title = $society->name."-".$request->input('bhk')."BHK"." ".$request->input('type');

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors()
            ], 422);
        }

        // dd($request->all());
        $ownerName = Auth::guard('owner')->user()->id;
        // dd($ownerName);
        $dynamicId = $this->dynamicUniqueId();

        $basePath = "property/{$ownerName}/{$dynamicId}/"; // Base path for the property
        $galleryPath = "{$basePath}/gallery/"; // Path for gallery images

        // dd($_FILES);
        $image = $request->file('image');
        $slug  = Str::slug($title);
        $imagename = null;
        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists($basePath)) {
                Storage::disk('public')->makeDirectory($basePath);
            }
            $propertyimage = Image::make($image)->stream();
            Storage::disk('public')->put($basePath . $imagename, $propertyimage);
        }


        $floor_plan = $request->file('floor_plan');
        if (isset($floor_plan)) {
            $currentDate = Carbon::now()->toDateString();
            $imagefloorplan = 'floor-plan-' . $currentDate . '-' . uniqid() . '.' . $floor_plan->getClientOriginalExtension();

            if (!Storage::disk('public')->exists($basePath)) {
                Storage::disk('public')->makeDirectory($basePath);
            }
            $propertyfloorplan = Image::make($floor_plan)->stream();
            Storage::disk('public')->put($basePath . $imagefloorplan, $propertyfloorplan);
        } else {
            $imagefloorplan = 'default.png';
        }
        $additionalDetail = $request->input('additional_detail') ? implode(',', explode(',', $request->input('additional_detail'))) : '';

        $property = new Property();
        $property->owner_id  = $ownerName;
        $property->unique_id = $dynamicId;
        $property->title = $title;
        $property->slug =  $slug;
        $property->price = $request->input('price') ?? null;
        $property->deposit = $request->input('deposit') ?? null;
        $property->monthly_rent = $request->input('monthly_rent') ?? null;
        $property->maintenance = $request->input('maintenance') ?? null;
        $property->tenant_type = $request->input('tenant_type');

        $property->furnish_type = $request->input('furnish_type');
        $property->city = $request->input('city');

        $property->additional_details = $additionalDetail ?? null;
        $property->locality = $request->input('locality');
        $property->society_name = $request->input('society_name');
        $property->area = $request->input('area');
        $property->description = $request->input('description');
        $property->purpose = $request->input('purpose');
        $property->type = $request->input('type');
        $property->bedroom = $request->input('bedroom');
        $property->bathroom = $request->input('bathroom');
        $property->balcony = $request->input('balcony') ?? 0;
        $property->bhk = $request->input('bhk') ?? 0;
        $property->available_for = $request->input('available_for');
        $property->age = $request->input('age') ?? null;
        $property->floor = $request->input('floor') ?? null;
        $property->total_floor = $request->input('total_floor') ?? null;
        $property->block_no = $request->input('block_no') ?? null;
        $property->flat_no = $request->input('flat_no') ?? null;
        $property->status = 'Request';
        // Store image paths
        $property->image = $imagename ?? null;
        $property->floor_plan = $imagefloorplan ?? null;
        $property->save();
        $features = is_string($request->features) ? json_decode($request->features, true) : $request->features;
        $amenities = is_string($request->amenities) ? json_decode($request->amenities, true) : $request->amenities;

        $property->features()->attach($features);
        $property->amenities()->sync($amenities);


        // Handle gallery images
        $gallary = $request->file('gallaryimage');
        // dd($gallary);
        if ($gallary) {
            foreach ($gallary as $images) {
                $currentDate = Carbon::now()->toDateString();
                $galimage['name'] = 'gallary-' . $currentDate . '-' . uniqid() . '.' . $images->getClientOriginalExtension();
                $galimage['size'] = $images->getSize();
                $galimage['property_id'] = $property->id;
                if (!Storage::disk('public')->exists($galleryPath)) {
                    Storage::disk('public')->makeDirectory($galleryPath);
                }
                // Load the image
                $propertyimage = Image::make($images);

                // Add text watermark
                $fontSize = 180;
                $propertyimage->text('RealTrust', $propertyimage->width() / 2, $propertyimage->height() / 2, function ($font) use ($fontSize) {
                    $font->size($fontSize);
                    $font->color(['r' => 230, 'g' => 228, 'b' => 231, 'a' => 0.5]);
                    $font->file(5);
                    $font->align('center');
                    $font->valign('middle');
                    $font->angle(0);
                });
                // Save the image with the watermark
                $propertyimageStream = $propertyimage->stream();
                Storage::disk('public')->put($galleryPath . $galimage['name'], $propertyimageStream);

                $property->gallery()->create($galimage);
            }
        }


        $staffMembers = Staff::all(); // Get all staff members
        foreach ($staffMembers as $staff) {
            $staff->notify(new StaffNotification($property->owner_id, $property->unique_id));
        }
        $notification = $staff->notifications()->latest()->first();
        $notificationId = $notification->id;
        broadcast(new EventsStaffNotification($property->owner_id, $property->unique_id, $notificationId));

        return response()->json([
            'message' => 'Property added successfully!',
            'property' => $property
        ], 201);
    }
    // Get properties that are for sale
    public function getForSaleProperties(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $properties = Property::where('looking_at', 'sell')->Where('status', 'active')->with('features', 'gallery', 'amenities')->paginate($perPage);
        if ($properties->isEmpty()) {
            return response()->json([
                'message' => 'No properties found.',
                'properties' => []
            ], 404);
        }
        return response()->json([
            'message' => 'properties list retrieved successfully.',
            'properties' => $properties
        ], 200);
    }

    // Get properties that are for rent
    public function getForRentProperties(Request $request)
    {

        $perPage = $request->input('per_page', 10);
        $properties = Property::where('looking_at', 'rent')->Where('status', 'active')->with('features', 'gallery', 'amenities')->paginate($perPage);
        if ($properties->isEmpty()) {
            return response()->json([
                'message' => 'No properties found.',
                'properties' => []
            ], 404);
        }
        return response()->json([
            'message' => 'properties list retrieved successfully.',
            'properties' => $properties
        ], 200);
    }
    public function filterProperties(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = Property::with('features', 'gallery', 'amenities');
        if ($request->has('location') && !empty($request->location)) {
            $query->where('city', $request->location);
            $query->orWhere('locality', $request->location);
        }
        if ($request->has('type') && !empty($request->type)) {
            $query->where('looking_at', $request->type);
        }
        if ($request->has('bhk') && !empty($request->bhk)) {
            $query->where('bhk', $request->bhk);
        }
        if ($request->has('furnish_type') && !empty($request->furnish_type)) {
            $query->where('furnish_type', $request->furnish_type);
        }
        if ($request->has('property_detail_type') && !empty($request->property_detail_type)) {
            $query->where('property_detail_type', $request->property_detail_type);
        }
        if ($request->has('rent') && !empty($request->rent)) {
            $query->where('rent', $request->rent);
        }
        $query->where('status', 'active');
        $properties = $query->paginate($perPage);
        if ($properties->isEmpty()) {
            return response()->json([
                'message' => 'No properties found.',
                'properties' => []
            ], 404);
        }
        return response()->json([
            'message' => 'properties list retrieved successfully.',
            'properties' => $properties
        ], 200);
    }


    public function edit($unique_id)
    {
        $property = Property::with('features', 'gallery', 'amenities', 'society', 'locality', 'city')->where('unique_id', $unique_id)->first();
        // dd($property);
        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        }
        $property->floor_plan = $property->flor_image_url;
        $property->image = $property->image_url;
        return response()->json([
            'success' => true,
            'data'    => $property
        ], 200);
    }

    public function update(Request $request, $unique_id)
    {
        $validator = Validator::make($request->all(), [
            'price'     => 'nullable',
            'deposit' => 'nullable',
            'monthly_rent' => 'nullable',
            'maintenance' => 'nullable',
            'type'      => 'required',
            'bedroom'   => 'required|integer|min:1',
            'bathroom'  => 'required|integer|min:1',
            'bhk'  => 'required|integer|min:1',
            'features' => 'required|string',
            'features.*' => 'integer|exists:features,id',
            'amenities' => 'required|string',
            'amenities.*' => 'integer|exists:amenities,id',
            'furnish_type' => 'required',
            'city'      => 'required|max:100',
            'locality'  => 'required|max:100',
            'area'      => 'required|numeric',
            'description' => 'required',
            'additional_detail' => 'required',
            'age' => 'required|integer|min:1|max:120',
            'available_for' => 'required|date',
            'block_no' => 'required',
            'flat_no' => 'required',
            
        ]);


        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Find property by unique_id
        $property = Property::where('unique_id', $unique_id)->first();

        $ownerName =  $property->owner_id;
        $dynamicId =  $property->unique_id;

        $basePath = "property/{$ownerName}/{$dynamicId}/"; // Base path for the property
        $galleryPath = "{$basePath}/gallery/"; // Path for gallery images

        $slug  = Str::slug($property->slug);
        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        }
        $image = $request->file('image');
            if (isset($image)) {
            
                $imageValidator = Validator::make(
                    ['image' => $image],
                    ['image' => 'image|mimes:jpeg,jpg,png|max:2048']
                );
            
                if ($imageValidator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation errors',
                        'errors'  => $imageValidator->errors()
                    ], 422);
                }

            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists($basePath)) {
                Storage::disk('public')->makeDirectory($basePath);
            }
            if (Storage::disk('public')->exists($basePath  . $property->image)) {
                Storage::disk('public')->delete($basePath  . $property->image);
            }
            $propertyimage = Image::make($image)->stream();
            Storage::disk('public')->put($basePath  . $imagename, $propertyimage);
        } else {
            // dd($property[0]->image);
            $imagename = $property->image;
        }

        $floor_plan = $request->file('floor_plan');
        if (isset($floor_plan)) {
            $imageValidator = Validator::make(
                    ['image' => $floor_plan],
                    ['image' => 'image|mimes:jpeg,jpg,png|max:2048']
                );
            
                if ($imageValidator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation errors',
                        'errors'  => $imageValidator->errors()
                    ], 422);
                }
            $currentDate = Carbon::now()->toDateString();
            $imagefloorplan = 'floor-plan-' . $currentDate . '-' . uniqid() . '.' . $floor_plan->getClientOriginalExtension();

            if (!Storage::disk('public')->exists($basePath)) {
                Storage::disk('public')->makeDirectory($basePath);
            }
            if (Storage::disk('public')->exists($basePath  . $property->floor_plan)) {
                Storage::disk('public')->delete($basePath  . $property->floor_plan);
            }

            $propertyfloorplan = Image::make($floor_plan)->stream();
            Storage::disk('public')->put($basePath  . $imagefloorplan, $propertyfloorplan);
        } else {
            $imagefloorplan = $property->floor_plan;
        }

        $additionalDetail = $request->input('additional_detail') ? implode(',', explode(',', $request->input('additional_detail'))) : '';

        $property->title = $property->title;
        $property->slug =  $slug;
        $property->price = $request->input('price') ?? $property->price;
        $property->deposit = $request->input('deposit') ?? null;
        $property->monthly_rent = $request->input('monthly_rent') ?? null;
        $property->maintenance = $request->input('maintenance') ?? null;

        $property->tenant_type = $request->input('tenant_type') ?? $property->tenant_type;
        $property->furnish_type = $request->input('furnish_type') ?? $property->furnish_type;
        $property->city = $request->input('city') ?? $property->city;
        $property->locality = $request->input('locality') ?? $property->locality;
        $property->area = $request->input('area') ?? $property->area;
        $property->description = $request->input('description') ?? $property->description;
        $property->purpose = $request->input('purpose') ?? $property->purpose;
        $property->type = $request->input('type') ?? $property->type;
        $property->bedroom = $request->input('bedroom') ?? $property->bedroom;
        $property->bathroom = $request->input('bathroom') ?? $property->bathroom;
        $property->balcony = $request->input('balcony') ?? $property->balcony;
        $property->bhk = $request->input('bhk') ?? $property->bhk;
        $property->available_for = $request->input('available_for') ?? $property->available_for;
        $property->latitude = $request->input('latitude') ?? $property->latitude;
        $property->age = $request->input('age') ?? $property->age;
        $property->longitude = $request->input('longitude') ?? $property->longitude;
        $property->society_name = $request->input('society_name') ?? $property->society_name;
        $property->additional_details = $additionalDetail ? $additionalDetail : $property->additional_details;
        $property->floor = $request->input('floor') ?? null;
        $property->total_floor = $request->input('total_floor') ?? null;
        $property->block_no = $request->input('block_no') ?? null;
        $property->flat_no = $request->input('flat_no') ?? null;

        // Store image paths
        $property->image = $imagename ?? null;
        $property->floor_plan = $imagefloorplan ?? null;

        // dd($request->features);
        $property->save();
        $features = is_string($request->features) ? explode(',', $request->features) : $request->features;
        $featuresList = array_unique($features);

        // foreach ($featuresList as $feature) {
        //     $property->features()->syncWithoutDetaching([$feature]);
        // }
        $property->features()->sync($featuresList);
        // Handle amenities
        $amenities = is_string($request->amenities) ? explode(',', $request->amenities) : $request->amenities;
        $amenitiesList = array_unique($amenities);
        $property->amenities()->sync($amenitiesList);

        $gallary = $request->file('gallaryimage');
        if ($gallary) {
            foreach ($gallary as $images) {
                if (isset($images)) {
                    $galleryImageValidator = Validator::make(
                        ['image' => $images],
                        ['image' => 'image|mimes:jpeg,jpg,png|max:2048']
                    );

                    if ($galleryImageValidator->fails()) {
                        if ($galleryImageValidator->fails()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Validation errors',
                                'errors'  => $galleryImageValidator->errors()
                            ], 422);
                        }
                    }

                    $currentDate = Carbon::now()->toDateString();
                    $galimage['name'] = 'gallary-' . $currentDate . '-' . uniqid() . '.' . $images->getClientOriginalExtension();
                    $galimage['size'] = $images->getSize();
                    $galimage['property_id'] = $property->id;

                    if (!Storage::disk('public')->exists($galleryPath)) {
                        Storage::disk('public')->makeDirectory($galleryPath);
                    }
                    // Load the image
                    $propertyimage = Image::make($images);

                    // Add text watermark
                    $fontSize = 180;
                    $propertyimage->text('RealTrust', $propertyimage->width() / 2, $propertyimage->height() / 2, function ($font) use ($fontSize) {
                        $font->size($fontSize);
                        $font->color(['r' => 230, 'g' => 228, 'b' => 231, 'a' => 0.5]);
                        $font->file(5);
                        $font->align('center');
                        $font->valign('middle');
                        $font->angle(0);
                    });
                    // Save the image with the watermark
                    $propertyimageStream = $propertyimage->stream();
                    Storage::disk('public')->put($galleryPath . $galimage['name'], $propertyimageStream);

                    $property->gallery()->create($galimage);
                }
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Property updated successfully!',
            'data'    => $property
        ], 200);
    }

    public function destroy($unique_id)
    {
        try {
            $property = Property::where('unique_id', $unique_id)->first();
            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found',
                ], 404);
            }
            $property->update(['status' => 'delete']);
            return response()->json([
                'success' => true,
                'message' => 'Property deleted successfully!'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while trying to update the property status.',
                'error' => $e->getMessage(),
            ], 500);
        }
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
            $owner =  Auth::guard('owner')->user();
            $query = Property::with('features', 'gallery', 'amenities', 'schedulePropertyTiming', 'society', 'locality', 'city')->where('owner_id', $owner->id);
            // dd($owner->id);

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

            if ($availableFilter !== 'All' && !empty($availableFilter)) {
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

    public function dynamicUniqueId()
    {
        $prefix = 'RELTRS';
        $date = now()->format('d');
        $randomLetters = Str::upper(Str::random(2));
        $randomNumber = mt_rand(10, 99);

        // Construct the unique ID
        $uniqueId = $prefix . $date . $randomLetters . $randomNumber;
        return $uniqueId;
    }


    public function getPropertyIntesrtListUser()
    {
        $user = Auth::guard('owner')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $id = $user->id;
        $scheduleVisit = UserInterest::with('property', 'user')->where('owner_id', $id)->get();
        if ($scheduleVisit->isEmpty()) {
            return response()->json(['message' => 'No properties found for this owner.'], 404);
        }
        return response()->json([
            'message' => 'Properties retrieved successfully.',
            'data' => $scheduleVisit
        ], 200);
    }
}
