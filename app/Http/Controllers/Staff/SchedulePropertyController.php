<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ConformTiming;
use App\Models\FieldManager;
use App\Models\ScheduleProperties;
use App\Models\ScheduleVisit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Support\Str;
use App\Models\Cities;

class SchedulePropertyController extends Controller
{
    public function index()
    {
        $visiterInfo = ScheduleProperties::with('property.owner', 'users', 'field_manager', 'schedule_visit_date')->orderBy('id', 'desc')->get();
        // dd($visiterInfo);
        return view('staff.schedule_properties.index', compact('visiterInfo'));
    }

    public function create()
    {
        return view('staff.schedule_properties.create');
    }

    public function getFieldManagerDetails(Request $request)
    {
        // dd($request->all());
        $fieldManager = FieldManager::with(['conform_timing' => function ($query) use ($request) {
            $query->where('property_id', $request->properties_id);
        }])->where('id', $request->id)->first();

        if (!$fieldManager) {
            return response()->json([
                'email' => null,
                'mobile_no' =>  null,
                'timing' =>  null,
            ]);
        }
        return response()->json([
            'email' => $fieldManager->email ?? null,
            'mobile_no' => $fieldManager->mobile_no ?? null,
            'timing' => $fieldManager->conform_timing && $fieldManager->conform_timing->conform_timing == 1
                ? Carbon::parse($fieldManager->conform_timing->timing)->format('l, F j, Y \a\t g:i A')
                : null,
        ]);
    }

    public function update_field_manager(Request $request)
    {


        $request->validate([
            'field_manager' => 'required|integer|exists:field_manager,id',
            'property_id' => 'required|string',
        ]);
        $fieldManagerId = $request->field_manager;
        $propertyId = $request->property_id;
        $conformTiming = ConformTiming::where('property_id', $propertyId)
            ->first();
        // dd($conformTiming);
        // If the conform_timing exists, update it
        if ($conformTiming) {
            $conformTiming->update([
                'field_manager_id' => $fieldManagerId
            ]);

            Toastr::success('message', 'Field manager updated successfully!');
        } else {
            ConformTiming::create([
                'property_id' => $propertyId,
                'field_manager_id' => $fieldManagerId,
            ]);

            Toastr::success('message', 'New field manager created successfully!');
        }
        return redirect()->route('staff.schedule_properties.visit', ['property_id' => $propertyId]);
    }
    public function visit($scheduleVisitId)
    {
        //  dd('d');
        $visiterInfo = ScheduleProperties::with(['property.owner', 'users', 'conform_timing.field_manager', 'schedule_visit_date.field_manager', 'property.society', 'property.localities', 'property.city'])
            ->where('property_id', $scheduleVisitId)
            ->whereHas('users', function ($query) {
                $query->whereColumn('schedule_properties.email', 'users.email');
            })->first();
        // dd($visiterInfo);
        $staff = Auth::guard('staff')->user();
        if ($staff && $visiterInfo) {  // Ensure $visiterInfo is not null
            if (is_null($visiterInfo->staff_id)) {
                $visiterInfo->staff_id = $staff->id;
                $visiterInfo->save();
            }
        } else {
            return response()->json(['message' => 'Unauthorized staff user.'], 401);
        }
        $fieldManager = FieldManager::all();
        // $user = User::all();
        $city = Cities::where('city_id', $visiterInfo->property->city)->first();
        return view('staff.schedule_properties.show', compact('visiterInfo', 'fieldManager', 'city'));
    }

    public function view($scheduleVisitId)
    {

        $visiterInfo = ScheduleProperties::with(['property.owner', 'conform_timing', 'schedule_visit_date.field_manager'])->where('property_id', $scheduleVisitId)->firstOrFail();

        // dd($scheduleVisitId);
        // $fieldManager = FieldManager::all();
        return view('staff.schedule_properties.view', compact('visiterInfo'));
    }
    public function store(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:field_manager,email|max:255',
            'mobile' => 'required|unique:field_manager,mobile_no|max:15',
            'password' => 'required|string|min:8',
            'image' => 'required|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Return back with errors and old input
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $image = $request->file('image');
        $slug  = Str::slug($request->name);

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('field_manager')) {
                Storage::disk('public')->makeDirectory('field_manager');
            }

            $owner =  Image::make($image)->stream();
            Storage::disk('public')->put('field_manager/' . $imagename, $owner);
        } else {
            $imagename = 'default.png';
        }

        $owner = FieldManager::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile_no' => $request->input('mobile'),
            'password' => Hash::make($request->input('password')),
            'image' => $imagename
        ]);
        Toastr::success('message', 'Owner add successfully.');
        return redirect()->route('staff.schedule_properties.index');
    }

    public function edit($id)
    {
        $field_manager = FieldManager::find($id);
        return view('staff.schedule_properties.edit', compact('field_manager'));
    }
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255'
        ]);
        // Check if validation fails
        if ($validator->fails()) {
            // Return back with errors and old input
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $image = $request->file('image');
        $slug  = Str::slug($request->name);
        $owner = FieldManager::find($id);

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('field_manager')) {
                Storage::disk('public')->makeDirectory('field_manager');
            }
            if (Storage::disk('public')->exists('field_manager/' . $owner->image)) {
                Storage::disk('public')->delete('field_manager/' . $owner->image);
            }
            $ownerImg = Image::make($image)->stream();
            Storage::disk('public')->put('field_manager/' . $imagename, $ownerImg);
        } else {
            $imagename = $owner->image;
        }

        $owner->name = $request->name;
        $owner->image = $imagename;
        $owner->save();

        Toastr::success('message', 'Owner updated successfully.');
        return redirect()->route('staff.schedule_properties.index');
    }

    public function destroy(string $id)
    {
        try {
            $scheduleVisit = ScheduleProperties::where('id', $id)->first();

            if (!$scheduleVisit) {
                Toastr::error('Schedule visit not found.', 'Error');
                return back()->withInput();
            }

            $scheduleVisit->delete();

            Toastr::success('Schedule visit deleted successfully.', 'Success');
            return back();
        } catch (\Exception $e) {
            Toastr::error('Failed to delete Schedule visit. Try again.', 'Error');
            return back()->withInput();
        }
    }
}
