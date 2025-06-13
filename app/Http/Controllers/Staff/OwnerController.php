<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\AgreementDetail;
use App\Models\AgreementLog;
use App\Models\EmailLog;
use App\Models\Owner;
use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Support\Str;

class OwnerController extends Controller
{
    public function index()
    {
        $owners = Owner::with('properties')->orderBy('id', 'desc')->get();

        return view('staff.owner.index', compact('owners'));
    }

    public function create()
    {
        return view('staff.owner.create');
    }
    public function add_number (Request $request){
        $owners = Owner::orderBy('id', 'desc')->get();
        return view('staff.owner.add-mask-number', compact('owners'));
    }
    public function storeMaskNumber (Request $request) {
        $validator = Validator::make($request->all(), [
            'ownerId' => 'required|exists:owners,id',
            'mask_number' => 'required|unique:owners,mask_mobile_no|max:11',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Return back with errors and old input
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $owner = Owner::find($request->ownerId);
        $owner->mask_mobile_no = $request->mask_number;
        $owner->save();
    
        return redirect()->route('staff.owner.index')->with('success', 'Mask number saved successfully!');

        
    }
    public function store(Request $request)
    {
        // dd($request->all());
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:owners,email|max:255',
            'mobile' => 'required|unique:owners,mobile_no|max:10',
            'image' => 'nullable|mimes:jpeg,jpg,png|max:2048',
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

            if (!Storage::disk('public')->exists('owners')) {
                Storage::disk('public')->makeDirectory('owners');
            }

            $ownerImg = Image::make($image)->stream();
            Storage::disk('public')->put('owners/' . $imagename, $ownerImg);
        } else {
            $imagename = null;
        }

        $owner = Owner::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile_no' => $request->input('mobile'),
            'image' => $imagename
        ]);
        Toastr::success('message', 'Owner add successfully.');
        return redirect()->route('staff.owner.index');
    }

    public function edit($id)
    {
        $owners = Owner::find($id);
        return view('staff.owner.edit', compact('owners'));
    }
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|mimes:jpeg,jpg,png',
            
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
        $owner = Owner::find($id);

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('owners')) {
                Storage::disk('public')->makeDirectory('owners');
            }
            if (Storage::disk('public')->exists('owners/' . $owner->image)) {
                Storage::disk('public')->delete('owners/' . $owner->image);
            }
            $ownerImg = Image::make($image)->stream();
            Storage::disk('public')->put('owners/' . $imagename, $ownerImg);
        } else {
            $imagename = $owner->image;
        }
        
        $owner->name = $request->name;
        $owner->image = $imagename;
        $owner->save();

        Toastr::success('message', 'Owner updated successfully.');
        return redirect()->route('staff.owner.index');
    }
    public function destroy(string $id)
    {
        $owner = Owner::find($id);
        $property = Property::where('owner_id',$owner->id)->get();
        if ($property->isNotEmpty()) {
            Toastr::error('Owner cannot be deleted as it has associated property.');
            return back();
        }
        if (Storage::disk('public')->exists('owners/' . $owner->image)) {
            Storage::disk('public')->delete('owners/' . $owner->image);
        }
        $owner->delete();
        Toastr::success('message', 'Owner deleted successfully.');
        return back();
    }


    public function showEmailForm($id)
    {
        $owners = Owner::find($id);
        return view('staff.owner.emails.send_mail', compact('owners'));
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'owner_name' => 'required|string',
            'mail_body' => 'required|string',
            'timing' => 'required',
        ]);

        $ownerName = $request->input('owner_name');
        $mailBody = $request->input('mail_body');
        $timing = $request->input('timing');

        // Schedule email
        $emailJob = (new SendEmailJob($ownerName, $timing, $mailBody));
        dispatch($emailJob);

        // Log email if using logging
        EmailLog::create([
            'owner_name' => $ownerName,
            'mail_body' => $mailBody,
            'sent_at' => $timing
        ]);
        Toastr::success('message', 'Email has been scheduled for sending!');
        return back();
    }

    public function viewOwnerProperties($ownerId)
    {
        $properties  = Property::with(['owner', 'schedule_visit'])->where('owner_id', $ownerId)->get();
        // dd($property);
        return view('staff.owner.property', compact('properties'));
    }

    public function getOwnerDetails($id)
    {
        $user = Owner::find($id);
        // dd($user);
        $agreements = AgreementDetail::with('properties', 'owner', 'user')->where('owner_id', $id)->orderBy('id', 'desc')->get();
        // dd($agreements);
        return view('staff.owner.verification', compact('user', 'agreements'));
    }

    public function getAgreementLog($id)
    {
        // dd($user);
        $agreements = AgreementDetail::with('properties', 'owner', 'user', 'agreementLogs')->orderBy('id', 'desc')->first();
        // dd($agreements[0]->property->owner_id);

        // dd($agreements);
        return view('staff.owner.agreement-list', compact('agreements'));
    }

    public function uploadDocument(Request $request)
    {
        try {
            // Validate request data
            $validatedData = $request->validate([
                'property_id' => 'required|string',
                'email' => 'required|string|exists:users,email',
                'owner_id' => 'required|integer',
                'agreement'   => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            ], [
                'email.exists' => 'The provided email is not registered in our system.', // Custom error message
            ]);


            // dd($request->all());
            // Check if the file exists
            if (!$request->hasFile('agreement')) {
                return back()->withErrors(['agreement' => 'No agreement file uploaded'])->withInput();
            }

            // Get file and generate filename
            $agreement = $request->file('agreement');
            $timestamp = Carbon::now()->format('Ymd_His');
            $agreementName = "agreement-{$timestamp}.{$agreement->getClientOriginalExtension()}";

            $storagePath = "property/{$validatedData['property_id']}/agreement/";

            // Ensure directory exists
            if (!Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->makeDirectory($storagePath);
            }

            // Try to store the file
            if (!$agreement->storeAs($storagePath, $agreementName, 'public')) {
                return back()->withErrors(['agreement' => 'Failed to upload agreement file'])->withInput();
            }

            // Find user by email
            $user = User::where('email', $validatedData['email'])->first();
            $user_id = $user ? $user->id : null;

            // Find the specific agreement record before updating
            $agreement = AgreementDetail::where('property_id', $validatedData['property_id'])->first();

            if ($agreement) {
                // Update the agreement
                $agreement->update([
                    'user_id' => $user_id,
                ]);
                // Get the updated agreement ID
                $agreement_id = $agreement->id;
                // $agreementLog = AgreementLog::where('property_id', $validatedData['property_id'])
                //     ->latest()
                //     ->first();
                $lastOwnerLog = AgreementLog::where('property_id', $validatedData['property_id'])
                    ->where('owner_approve', 1)->where('agreement_id', $agreement_id)->where('owner_id', $validatedData['owner_id'])
                    ->latest('created_at')
                    ->first();

                $lastUserLog = AgreementLog::where('property_id', $validatedData['property_id'])
                    ->where('user_approve', 1)->where('agreement_id', $agreement_id)->where('user_id', $user_id)
                    ->latest('created_at')
                    ->first();
                if ($lastOwnerLog && $lastUserLog) {
                    $agreementLog = AgreementLog::create([
                        'agreement_id' => $agreement_id,
                        'property_id' => $validatedData['property_id'],
                        'owner_id'    => $validatedData['owner_id'],
                        'user_id'     => $user_id,
                        'agreement'   => $agreementName,
                        'owner_approve'   => $lastOwnerLog['owner_approve'],
                        'user_approve'   => $lastUserLog['user_approve'],
                        'highlight_owner'      =>  1,
                        'highlight_user'      =>  1,
                        'notary' => 1,
                        'remark'      => '',
                        'description' => 'Notary Agreement uploaded by staff.',
                        'created_at'  => now(),
                    ]);
                } else {
                    $agreementLog = AgreementLog::create([
                        'agreement_id' => $agreement_id,
                        'property_id' => $validatedData['property_id'],
                        'owner_id'    => $validatedData['owner_id'],
                        'user_id'     => $user_id,
                        'agreement'   => $agreementName,
                        'highlight_owner'      =>  1,
                        'highlight_user'      =>  1,
                        'notary' => 0,
                        'owner_approve_btn' => 1,
                        'user_approve_btn' => 1,
                        'remark'      => '',
                        'description' => 'Agreement uploaded by staff.',
                        'created_at'  => now(),
                    ]);
                }
            }
            // Save agreement log
            if (!$agreementLog) {
                return back()->withErrors(['database' => 'Failed to save agreement details'])->withInput();
            }

            return redirect()->route('owner.agreement_logs', ['id' => $validatedData['property_id']])
                ->with('success', 'Agreement uploaded successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()])->withInput();
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'verification' => 'required|boolean',
        ]);

        try {
            $user = Owner::findOrFail($id);

            // Check if all required documents are available
            if (empty($user->electricity_bill) || empty($user->pan_card)) {
                Toastr::error('All required documents must be uploaded before verification.', 'Error');
                return back();
            }

            // If all documents are available, proceed with the verification
            $user->verification = $request->verification;
            $user->save();

            Toastr::success('Verification status updated successfully.', 'Success');
            return back();
        } catch (ModelNotFoundException $e) {
            Toastr::error('User not found.', 'Error');
            return back();
        } catch (Exception $e) {
            Toastr::error('An error occurred while updating the verification status.', 'Error');
            return back();
        }
    }
}
