<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleVisit;
use App\Models\ScheduleVisitUserList;
use App\Models\User;
use App\Models\UserInterest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Support\Str;
use App\Models\UserDocument;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|unique:users|max:255',
            'mobile' => 'required|digits:10|unique:owners,mobile_no',
            'image' => 'nullable|mimes:jpeg,jpg,png,webp'
        ]);

        $image = $request->file('image');
        $slug  = Str::slug($request->name);

        if (isset($image)) {
            
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('users')) {
                Storage::disk('public')->makeDirectory('users');
            }

            $user =  Image::make($image)->stream();
            Storage::disk('public')->put('users/' . $imagename, $user);
        } else {
            $imagename = NULL;
        }
        
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'image' => $imagename,
            'mobile_no' => $request->input('mobile'),
            'role_id' => '3'
        ]);

        Toastr::success('message', 'User created successfully.');
        return redirect()->route('admin.user.index');
    }

    public function edit($id)
    {
        $user = User::find($id);
        return view('admin.users.edit', compact('user'));
    }
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|mimes:jpeg,jpg,png',
            'email' => 'required|unique:users,email,' . $id,
        ]);

        $image = $request->file('image');
        $slug  = Str::slug($request->name);
        $user = User::find($id);

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('users')) {
                Storage::disk('public')->makeDirectory('users');
            }
            if (Storage::disk('public')->exists('users/' . $user->image)) {
                Storage::disk('public')->delete('users/' . $user->image);
            }
            $userimg = Image::make($image)->stream();
            Storage::disk('public')->put('users/' . $imagename, $userimg);
        } else {
            $imagename = $user->image;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->image = $imagename;
        $user->save();

        Toastr::success('message', 'User updated successfully.');
        return redirect()->route('admin.user.index');
    }
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (Storage::disk('public')->exists('users/' . $user->image)) {
            Storage::disk('public')->delete('users/' . $user->image);
        }
        $user->delete();
        Toastr::success('message', 'User deleted successfully.');
        return back();
    }

    public function getUserDetails($id)
    {
        $user = User::with('userDocuments')->find($id);
        return view('admin.users.verification', compact('user'));
    }

    public function updateStatus(Request $request, $id)
    {
        // dd($id);
        $request->validate([
            'verification' => 'required|boolean',
        ]);
        // dd($request->all());
        try {
            $userdoc = UserDocument::where('user_id',$id)->first();
            $user = User::findOrFail($id);

            // Check if all required documents are available
            if (empty($userdoc->aadhaar_card) || empty($userdoc->pan_card) || empty($userdoc->employee_id)) {
                Toastr::error('All required documents must be uploaded before verification.', 'Error');
                return back();
            }

            // If all documents are available, proceed with the verification
            $user->verification = $request->verification;
            $user->save();

            Toastr::success('Verification status updated successfully.', 'Success');
            return back();
        } catch (\Exception $e) {
            // Handle the error if the user is not found or any other exception occurs
            Toastr::error('User not found.', 'Error');
            return back();
        }
    }

    public function inquery(Request $request, $id)
    {
        // dd($request->all());
        // dd($visiter);

        $UserInterest = UserInterest::where('user_id', $id)->first();
        // dd($UserInterest);
        if ($UserInterest) {
            return view('conform-time-success', ['message' => 'You have already confirmed your timing at ' . Carbon::parse($UserInterest->updated_at)->format('Y-m-d h:i A')]);
        } else {

             $visiter = ScheduleVisitUserList::where('user_id', $id)->select('visite_id')->first();
            $detial = ScheduleVisit::with('property')->where('id', $visiter->visite_id)->first();
            $user = User::where('id', $id)->first();
            // dd($detial,$user);
            return view('inquery', compact('detial', 'user'));
        }
    }
    public function submitUserInterest(Request $request)
    {
        // dd($request->all());
        try {
            // Validation rules
            $validator = Validator::make($request->all(), [
                'property_name' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'locality' => 'required|string|max:255',
                'society' => 'required|string|max:255',
                'price' => 'required|numeric|min:1',
                'address' => 'required|string',
                'final_rent' => 'required|numeric|min:1',
                'deposit' => 'required|numeric|min:1',
                'maintenance' => 'required|numeric|min:0',
                'name' => 'required|string|max:255',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            //  dd($_FILES);
            // Insert data into the database
            $userInterest = UserInterest::create([
                'user_id' => $request->input('user_id'),
                'property_id' => $request->input('property_id'),
                'final_rent' => $request->input('final_rent'),
                'deposit' => $request->input('deposit'),
                'maintenance_per_month' => $request->input('maintenance'),
                'owner_id' => null, // Adjust based on your logic
                'status' => 'pending', // Default status
            ]);
            $user = User::where('id', $request->input('user_id'))->first();
            $userId = $user->id;
            $currentTimestamp = Carbon::now()->format('Ymd_His');
            if ($request->hasFile('aadhaar_image')) {
                $aadhaarCard = $request->file('aadhaar_image');
                $aadhaarCardName = 'aadhaar-' . $currentTimestamp . '.' . $aadhaarCard->getClientOriginalExtension();
                $aadhaarCardDirectory = 'users/' . $userId . '/documents';

                if (!Storage::disk('public')->exists($aadhaarCardDirectory)) {
                    Storage::disk('public')->makeDirectory($aadhaarCardDirectory);
                }
                $aadhaarCardPath = $aadhaarCard->storeAs($aadhaarCardDirectory, $aadhaarCardName, 'public');
                $user->aadhaar_card = $aadhaarCardName;
            }

            if ($request->hasFile('pan_image')) {
                $panCard = $request->file('pan_image');
                $panCardName = 'pan-' . $currentTimestamp . '.' . $panCard->getClientOriginalExtension();
                $panCardDirectory = 'users/' . $userId . '/documents';

                if (!Storage::disk('public')->exists($panCardDirectory)) {
                    Storage::disk('public')->makeDirectory($panCardDirectory);
                }

                $panCardPath = $panCard->storeAs($panCardDirectory, $panCardName, 'public');
                $user->pan_card = $panCardName;
            }

            if ($request->hasFile('agreement_image')) {
                $agreement = $request->file('agreement_image');
                $userName = preg_replace('/[^A-Za-z0-9\-]/', '_', $user->name); // sanitize the user name to avoid invalid characters in file names
                $currentTimestamp = Carbon::now()->format('Ymd_His');
                $agreementName = 'agreement-' . $currentTimestamp . '.' . $agreement->getClientOriginalExtension();

                $userId = $user->id; // Get user ID
                $agreementPath = 'users/' . $userId . '/documents';

                if (!Storage::disk('public')->exists($agreementPath)) {
                    Storage::disk('public')->makeDirectory($agreementPath);
                }
                $agreement->storeAs($agreementPath, $agreementName, 'public');
                $user->agreement = $agreementName;
            }
            $user->save();

            // Return success response
            return view('conform-time-success', ['message' => 'User interest submitted successfully. Thank you! ']);
        } catch (\Exception $e) {

            return view('conform-time-success', ['message' => 'An error occurred while submitting user interest.']);
        }
    }
}
