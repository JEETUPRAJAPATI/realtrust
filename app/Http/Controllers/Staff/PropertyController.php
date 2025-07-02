<?php

namespace App\Http\Controllers\Staff;

use App\Events\PropertyStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\Amenities;
use App\Models\Feature;
use App\Models\Locality;
use App\Models\Owner;
use App\Models\Property;
use App\Models\PropertyImageGallery;
use App\Models\ScheduleProperties;
use App\Notifications\PropertyStatusUpdatedNotification;
use Carbon\Carbon;
use Flasher\Prime\Notification\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Yoeunes\Toastr\Facades\Toastr;
use App\Models\AdditionalDetail;
use App\Models\Cities;
use App\Models\Society;


class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::latest()->with('owner', 'schedule_visit', 'amenities', 'gallery', 'features', 'society', 'localities')->get();

        // dd($properties->toArray());
        // dd($properties);
        return view('staff.properties.index', compact('properties'));
    }
    public function filterStatus($status)
    {
        $properties = Property::where('status', $status)
            ->latest()
            ->with('owner', 'schedule_visit', 'amenities', 'gallery', 'features')
            ->get();
        return view('staff.properties.index', compact('properties'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $features = Feature::all();
        $owners = Owner::all();
        $amenities = Amenities::all();
        $locality = Locality::where('cities_id', 57933)->get();
        $additionalsDetail  = AdditionalDetail::all();
        return view('staff.properties.create', compact('features', 'owners', 'amenities', 'locality', 'additionalsDetail'));
    }


    public function store(Request $request)
    {
        // dd($request->all());
        $rules = [
            // 'title'     => 'required|unique:properties|max:255',
            'owner' => 'required',
            'purpose'   => 'required|in:sell,rent,upcoming_projects',
            'type'      => 'required',
            'bedroom'   => 'required|integer|min:1',
            'bathroom'  => 'required|integer|min:1',
            'bhk'       => 'required|integer|min:1',
            'features'  => 'required',
            'amenities' => 'required',
            'furnish_type' => 'required',
            'city'      => 'required|max:100',
            'locality'  => 'required|max:100',
            'society_name' => 'required',
            'area'      => 'required|numeric',
            'floor_plan' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'additional_detail' => 'required',
            'image'     => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'gallaryimage.*' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'description' => 'required',
            'age'       => 'required|integer|min:1|max:120',
            'available_for' => 'required|date',
            'floor'     => 'required|integer|min:1',
            'pdf_file'  => 'nullable|mimes:pdf|max:10240',
            'youtube_urls.*' => 'nullable|url',
            'block_no'  => 'required',
            'flat_no'   => 'required',
        ];

        // Add extra rules if purpose is rent
        if ($request->purpose == 'rent') {
            $rules = array_merge($rules, [
                'deposit' => 'required|numeric',
                'monthly_rent' => 'required|numeric',
                'maintenance' => 'required|numeric',
            ]);
        } elseif ($request->purpose == 'sell') {
            $rules = array_merge($rules, [
                'price' => 'required',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $society = Society::where('id', $request->input('society_name'))->first();
        $title = $society->name . "-" . $request->input('bhk') . "BHK" . " " . $request->input('type');

        //  dd($request->all());
        // dd($_FILES);
        $ownerName = $request->input('owner');
        $dynamicId = $this->dynamicUniqueId();

        $basePath = "property/{$ownerName}/{$dynamicId}/"; // Base path for the property
        $galleryPath = "{$basePath}/gallery/"; // Path for gallery images


        $image = $request->file('image');
        $slug  = Str::slug($title);
        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists($basePath)) {
                Storage::disk('public')->makeDirectory($basePath);
            }
            $propertyimage = Image::make($image)->stream();
            Storage::disk('public')->put($basePath . $imagename, $propertyimage);
        }

        $pdf = $request->file('pdf_file');
        $pdfname = null;
        if (isset($pdf)) {
            $currentDate = Carbon::now()->toDateString();
            $pdfname = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $pdf->getClientOriginalExtension();

            // Check if the directory exists; create it if not
            if (!Storage::disk('public')->exists($basePath)) {
                Storage::disk('public')->makeDirectory($basePath);
            }
            Storage::disk('public')->putFileAs($basePath, $pdf, $pdfname);
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
        // $additionalDetail = $request->input('additional_detail') ? implode(',', explode(',', $request->input('additional_detail'))) : '';
        $additionalDetail = is_array($request->input('additional_detail'))
            ? implode(',', $request->input('additional_detail'))
            : $request->input('additional_detail', '');

        $property = new Property();
        if (isset($request->featured)) {
            $property->featured = true;
        }
        // Create Property
        $property = new Property();
        $property->owner_id  = $ownerName;
        $property->unique_id = $dynamicId;
        $property->title = $title;
        $property->slug =  $slug;
        $property->price = $request->input('price');
        $property->deposit = $request->input('deposit') ?? null;
        $property->monthly_rent = $request->input('monthly_rent') ?? null;
        $property->maintenance = $request->input('maintenance') ?? null;
        $property->tenant_type = $request->input('tenant_type');
        $property->pdf_file = $pdfname;
        $property->highlight_type = $request->input('highlight') ?? null;
        $property->furnish_type = $request->input('furnish_type');
        $property->city = $request->input('city');
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
        $property->floor = $request->input('floor') ?? null;
        $property->total_floor = $request->input('total_floor') ?? null;
        $property->additional_details = $additionalDetail ?? null;
        $property->age = $request->input('age') ?? null;
        $property->price_range = $request->input('price_range') ?? null;
        $property->block_no = $request->input('block_no') ?? null;
        $property->flat_no = $request->input('flat_no') ?? null;
        // Store image paths
        $property->image = $imagename ?? null;
        $property->floor_plan = $imagefloorplan ?? null;
        $property->save();
        $property->features()->attach($request->features);
        $property->amenities()->attach($request->amenities);


        $youtubeUrls = $request->input('youtube_urls', []); // Retrieve YouTube URLs from the request
        $propertyId = $property->id; // Property ID for the association

        if (!empty($youtubeUrls)) {
            foreach ($youtubeUrls as $url) {
                if (!empty($url)) {
                    $galleryData = [
                        'name' => $url,
                        'size' => null,
                        'property_id' => $propertyId,
                    ];
                    $property->gallery()->create($galleryData);
                }
            }
        }

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

        Toastr::success('message', 'Property created successfully.');
        return redirect()->route('staff.properties.index');
    }


    public function show($slug)
    {
        $property = Property::with(['owner', 'gallery'])->where('slug', $slug)->firstOrFail();
        // dd('dsa');
        $localityName = Locality::where('id', $property->locality)->value('name');
        $society = Society::where('id', $property->society_name)->first();
        $city = Cities::where('city_id', $property->city)->first();
        return view('staff.properties.show', compact('property', 'localityName', 'society', 'city'));
    }
    public function view($id)
    {
        $property_id = $id ?? null;
        // dd('dsa');
        $property = Property::with(['owner', 'schedule_visit', 'amenities', 'gallery', 'features', 'society', 'localities'])->where('unique_id', $property_id)->firstOrFail();
        $city = Cities::where('city_id', $property->city)->first();
        return view('staff.properties.show', compact('property', 'city'));
    }

    public function edit($slug)
    {
        $features = Feature::all();
        $property = Property::with('gallery')->where('slug', $slug)->firstOrFail();
        $owners = Owner::all();
        $amenities = Amenities::all();
        $locality = Locality::where('cities_id', 57933)
            ->get();
        $additionalsDetail  = AdditionalDetail::all();
        return view('staff.properties.edit', compact('property', 'features', 'owners', 'amenities', 'locality', 'additionalsDetail'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
        ]);

        $property = Property::findOrFail($id);
        if ($property) {
            $property->status = $request->status;
            $property->save();
            return response()->json([
                'success' => true,
                'message' => 'Status updated to ' . $request->status
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Property not found.'
        ]);
    }

    public function update(Request $request, $slug)
    {

        $property = Property::where('slug', $slug)->firstOrFail();
        // dd($request->all());
        // 'title'     => ['required', 'max:255', Rule::unique('properties')->ignore($property->id)],
        // $validator = Validator::make($request->all(), [
        //     'deposit' => 'nullable|numeric',
        //     'monthly_rent' => 'nullable|numeric',
        //     'maintenance' => 'nullable|numeric',
        //     'purpose'   => 'required|in:sell,rent,upcoming_projects',
        //     'type'      => 'required',
        //     'bedroom'   => 'required|integer|min:1',
        //     'bathroom'  => 'required|integer|min:1',
        //     'bhk'  => 'required|integer|min:1',
        //     'features' => 'required',
        //     'amenities' => 'required',
        //     'furnish_type' => 'required',
        //     'city'      => 'required|max:100',
        //     'locality'  => 'required|max:100',
        //     'area'      => 'required|numeric',
        //     'floor_plan' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        //     'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        //     'gallaryimage.*' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        //     'description' => 'required',
        //     'additional_detail' => 'required',
        //     'age' => 'required|integer|min:1|max:120',
        //     'available_for' => 'required|date',
        //     'floor'  => 'required|integer|min:1',
        //     'pdf_file' => 'nullable|mimes:pdf|max:10240',
        //     'youtube_urls.*' => 'nullable|url',
        //     'block_no' => 'required',
        //     'flat_no' => 'required',
        // ]);
        $rules = [
            'purpose'   => 'required|in:sell,rent,upcoming_projects',
            'type'      => 'required',
            'bedroom'   => 'required|integer|min:1',
            'bathroom'  => 'required|integer|min:1',
            'bhk'       => 'required|integer|min:1',
            'features'  => 'required',
            'amenities' => 'required',
            'furnish_type' => 'required',
            'city'      => 'required|max:100',
            'locality'  => 'required|max:100',
            'society_name' => 'required',
            'area'      => 'required|numeric',
            'floor_plan' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'additional_detail' => 'required',
            'image'     => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'gallaryimage.*' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'description' => 'required',
            'age'       => 'required|integer|min:1|max:120',
            'available_for' => 'required|date',
            'floor'     => 'required|integer|min:1',
            'pdf_file'  => 'nullable|mimes:pdf|max:10240',
            'youtube_urls.*' => 'nullable|url',
            'block_no'  => 'required',
            'flat_no'   => 'required',
        ];

        // Add extra rules if purpose is rent
        if ($request->purpose == 'rent') {
            $rules = array_merge($rules, [
                'deposit' => 'required|numeric',
                'monthly_rent' => 'required|numeric',
                'maintenance' => 'required|numeric',
            ]);
        } elseif ($request->purpose == 'sell') {
            $rules = array_merge($rules, [
                'price' => 'required',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $ownerName =  $property->owner_id;
        $dynamicId =  $property->unique_id;

        $basePath = "property/{$ownerName}/{$dynamicId}/"; // Base path for the property
        $galleryPath = "{$basePath}/gallery/"; // Path for gallery images

        $image = $request->file('image');
        $slug  = Str::slug($slug);
        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists($basePath)) {
                Storage::disk('public')->makeDirectory($basePath);
            }
            if (Storage::disk('public')->exists($basePath . $property->image)) {
                Storage::disk('public')->delete($basePath . $property->image);
            }
            $propertyimage = Image::make($image)->stream();
            Storage::disk('public')->put($basePath . $imagename, $propertyimage);
        } else {
            // dd($property[0]->image);
            $imagename = $property->image;
        }
        $pdf = $request->file('pdf_file');
        $pdfname = null;
        if (isset($pdf)) {
            $currentDate = Carbon::now()->toDateString();
            $pdfname = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $pdf->getClientOriginalExtension();

            // Check if the directory exists; create it if not
            if (!Storage::disk('public')->exists($basePath)) {
                Storage::disk('public')->makeDirectory($basePath);
            }
            Storage::disk('public')->putFileAs($basePath, $pdf, $pdfname);
        } else {
            $pdfname = $property->pdf_file;
        }

        $floor_plan = $request->file('floor_plan');
        if (isset($floor_plan)) {
            $currentDate = Carbon::now()->toDateString();
            $imagefloorplan = 'floor-plan-' . $currentDate . '-' . uniqid() . '.' . $floor_plan->getClientOriginalExtension();

            if (!Storage::disk('public')->exists($basePath)) {
                Storage::disk('public')->makeDirectory($basePath);
            }
            if (Storage::disk('public')->exists($basePath . $property->floor_plan)) {
                Storage::disk('public')->delete($basePath . $property->floor_plan);
            }

            $propertyfloorplan = Image::make($floor_plan)->stream();
            Storage::disk('public')->put($basePath . $imagefloorplan, $propertyfloorplan);
        } else {
            $imagefloorplan = $property->floor_plan;
        }
        $additionalDetail = is_array($request->input('additional_detail'))
            ? implode(',', $request->input('additional_detail'))
            : $request->input('additional_detail', '');

        $property->unique_id = $property->unique_id;
        $property->title = $property->title;
        $property->slug =  $slug;
        $property->price = $request->input('price');
        $property->deposit = $request->input('deposit') ?? null;
        $property->highlight_type = $request->input('highlight') ?? null;
        $property->monthly_rent = $request->input('monthly_rent') ?? null;
        $property->maintenance = $request->input('maintenance') ?? null;
        $property->tenant_type = $request->input('tenant_type');
        $property->pdf_file = $pdfname;
        $property->furnish_type = $request->input('furnish_type');
        $property->city = $request->input('city');
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
        $property->additional_details = $additionalDetail ? $additionalDetail : $property->additional_details;
        $property->floor = $request->input('floor') ?? null;
        $property->total_floor = $request->input('total_floor') ?? null;
        $property->price_range = $request->input('price_range') ?? null;
        $property->block_no = $request->input('block_no') ?? null;
        $property->flat_no = $request->input('flat_no') ?? null;
        // Store image paths
        $property->image = $imagename ?? null;
        $property->floor_plan = $imagefloorplan ?? null;

        $property->save();
        $property->features()->sync($request->features);
        $property->amenities()->sync($request->amenities);


        $youtubeUrls = $request->input('youtube_urls', []);
        $propertyId = $property->id;
        // $property->gallery()->delete();
        if (!empty($youtubeUrls)) {
            foreach ($youtubeUrls as $url) {
                if (!empty($url)) {
                    $galleryData = [
                        'name' => $url,
                        'size' => null,
                        'property_id' => $propertyId,
                    ];
                    $property->gallery()->create($galleryData);
                }
            }
        }
        $gallary = $request->file('galleryimage');

        if (isset($gallary)) {
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
        Toastr::success('message', 'Property updated successfully.');
        return redirect()->route('staff.properties.index');
    }


    public function destroy($slug)
    {
        $property = Property::where('slug', $slug)->firstOrFail();

        if (Storage::disk('public')->exists('property/' . $property->owner_id . '/' . $property->unique_id . '/gallery/' . $property->image)) {
            Storage::disk('public')->delete('property/' . $property->owner_id . '/' . $property->unique_id . '/gallery/' . $property->image);
        }

        // Delete floor plan if exists
        if (Storage::disk('public')->exists('property/' . $property->owner_id . '/' . $property->unique_id . '/' . $property->floor_plan)) {
            Storage::disk('public')->delete('property/' . $property->owner_id . '/' . $property->unique_id . '/' . $property->floor_plan);
        }


        $galleries = $property->gallery;
        if ($galleries) {
            foreach ($galleries as $key => $gallery) {
                $galleryPath = 'property/' . $property->owner_id . '/' . $property->unique_id . '/gallery/' . $gallery->name;
                if (Storage::disk('public')->exists($galleryPath)) {
                    Storage::disk('public')->delete($galleryPath);
                }
                PropertyImageGallery::destroy($gallery->id);
            }
        }

        $property->features()->detach();
        $property->delete();
        Toastr::success('message', 'Property deleted successfully.');
        return back();
    }


    public function galleryImageDelete(Request $request)
    {

        $gallery = PropertyImageGallery::find($request->id);

        if ($gallery) {
            // Delete gallery image if it exists
            $galleryPath = 'property/' . $gallery->property->owner_id . '/' . $gallery->property->unique_id . '/gallery/' . $gallery->name;
            if (Storage::disk('public')->exists($galleryPath)) {
                Storage::disk('public')->delete($galleryPath);
            }

            // Delete the gallery image record
            $gallery->delete();

            if ($request->ajax()) {
                return response()->json(['msg' => true]);
            }
        }
        return response()->json(['msg' => false], 404);
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


    public function userList($property_id)
    {
        $visiterInfo  = ScheduleProperties::with('schedule_visit', 'users')->where('property_id', $property_id)->get();
        // dd($visiterInfo);

        return view('staff.properties.user', compact('visiterInfo'));
    }
}
