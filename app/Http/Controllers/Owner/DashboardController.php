<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\OwnerResource;
use App\Models\Notifications;
use App\Models\Owner;
use App\Models\Property;
use App\Models\ScheduleVisit;
use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;
use App\Http\Resources\UserResource;
use App\Models\AgreementDetail;
use App\Models\AgreementLog;
use App\Models\ScheduleVisitUserList;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {

        $id = Auth::guard('owner')->user()->id;
        $owners = Owner::with('properties')->where('id', $id)->get();
        if (!$owners) {
            return response()->json([
                'success' => false,
                'message' => 'Owner not found.'
            ], 404);
        }
        // Initialize counts array
        $counts = [
            'Active' => 0,
            'Inactive' => 0,
            'Reject' => 0,
            'Request' => 0,
            'Expired' => 0,
            'Delete' => 0
        ];

        // Iterate through each owner and their properties
        foreach ($owners as $owner) {
            foreach ($owner->properties as $property) {
                if (array_key_exists($property->status, $counts)) {
                    $counts[$property->status]++;
                }
            }
        }

        if (array_sum($counts) == 0) {
            return response()->json([
                'message' => 'No properties found.',
                'properties' => [],
                'counts' => $counts
            ], 200);
        }

        return response()->json([
            'message' => 'Properties list retrieved successfully.',
            'counts' => $counts
        ], 200);
    }


    public function profile()
    {
        $profile = Auth::guard('owner')->user();

        if ($profile) {
            return response()->json([
                'status' => true,
                'message' => 'Profile Data retrieved successfully.',
                'data' => new OwnerResource($profile) // Transform user data using UserResource
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Profile not found.',
                'data' => null
            ], 404);
        }
    }

    public function profileUpdate(Request $request)
    {
        $profile = Auth::guard('owner')->user();
        if (!$profile) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized owner.'
            ], 404);
        }

        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'mobile_no' => [
                'required',
                'numeric',
                'digits_between:10,15',
                Rule::unique('owners', 'mobile_no')->ignore($profile->id)
            ],
            'email' => [
                'required',
                'string',
                Rule::unique('owners', 'email')->ignore($profile->id)
            ],
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,avif|max:2048',
            'electricity_bill' => 'file|mimes:pdf,jpeg,png,jpg,webp,avif|max:2048',
            'pan_card' => 'file|mimes:pdf,jpeg,png,jpg,webp,avif|max:2048'
        ];

        // Validate the request
        try {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
        $slug  = Str::slug($request->name);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('owners')) {
                Storage::disk('public')->makeDirectory('owners');
            }
            if (Storage::disk('public')->exists('owners/' . $profile->image)) {
                Storage::disk('public')->delete('owners/' . $profile->image);
            }
            $ownerImg = Image::make($image)->stream();
            Storage::disk('public')->put('owners/' . $imagename, $ownerImg);
        } else {
            $imagename = $profile->image;
        }
        $currentTimestamp = Carbon::now()->format('Ymd_His');
        $userId = $profile->id; // Get the owner ID
        // pan card upload
        if ($request->hasFile('pan_card')) {
                $panCard = $request->file('pan_card');
                $panCardName = 'pan-' . $currentTimestamp . '.' . $panCard->getClientOriginalExtension();
                $panCardDirectory = 'owners/pan_card' . $userId . '/documents';

                if (!Storage::disk('public')->exists($panCardDirectory)) {
                    Storage::disk('public')->makeDirectory($panCardDirectory);
                }

                $panCardPath = $panCard->storeAs($panCardDirectory, $panCardName, 'public');
                $profile->pan_card = $panCardName;
            }
        if ($request->hasFile('electricity_bill')) {
                $electricityBill = $request->file('electricity_bill');
                $electricityBillName = 'electricity_bill-' . $currentTimestamp . '.' . $electricityBill->getClientOriginalExtension();
                $electricityBillDirectory = 'owners/electricity_bill' . $userId . '/documents';

                if (!Storage::disk('public')->exists($electricityBillDirectory)) {
                    Storage::disk('public')->makeDirectory($electricityBillDirectory);
                }
                $electricityBillPath = $electricityBill->storeAs($electricityBillDirectory, $electricityBillName, 'public');
                $profile->electricity_bill = $electricityBillName;
            }    
        // Update the user's profile information
        $profile->name = $request->name;
        $profile->email = $request->email;
        $profile->mobile_no = $request->mobile_no;
        $profile->image = $imagename;
        $profile->save();

        // Return a successful response
        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully.',
            'data' =>  new OwnerResource($profile)
        ], 200);
    }

    public function getTenantDocument()
    {

        $visitedUserslist = ScheduleVisit::where('owner_id', Auth::guard('owner')->user()->id)->get();

        $documents = [];

        foreach ($visitedUserslist as $list) {
            $userIds = ScheduleVisitUserList::where('visite_id', $list->id)->pluck('user_id')->toArray();
            $documentsItems = User::with('userDocuments')->whereIn('id', $userIds)->where('verification', 1)->get();
            $propertyName = Property::where('unique_id', $list->property_id)->value('title'); // Using value() to get a single title

            foreach ($documentsItems as $document) {
                $userResource = (new UserResource($document))->toArray(request());

                // Append property_name to each document
                $userResource['property_name'] = $propertyName;

                // Add to the documents array
                $documents[] = $userResource;
            }
        }


        return response()->json([
            'message' => 'Documents retrieved successfully',
            'users' => $documents
        ], 200);
    }

    public function uploadDocuments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'nullable|string|max:255',
            'employee_id' => 'nullable|string|max:255',
            'aadhaar_card' => 'file|mimes:pdf,jpeg,png,jpg|max:2048',
            'pan_card' => 'file|mimes:pdf,jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            $document = Auth::guard('owner')->user();
            $document->company_name = $request->company_name;
            $document->employee_id = $request->employee_id;
            $currentTimestamp = Carbon::now()->format('Ymd_His');
            $userId = $document->id; // Get the owner ID

            if ($request->hasFile('aadhaar_card')) {
                $aadhaarCard = $request->file('aadhaar_card');
                $aadhaarCardName = 'aadhaar-' . $currentTimestamp . '.' . $aadhaarCard->getClientOriginalExtension();
                $aadhaarCardDirectory = 'owners/' . $userId . '/documents';

                if (!Storage::disk('public')->exists($aadhaarCardDirectory)) {
                    Storage::disk('public')->makeDirectory($aadhaarCardDirectory);
                }

                $aadhaarCardPath = $aadhaarCard->storeAs($aadhaarCardDirectory, $aadhaarCardName, 'public');
                $document->aadhaar_card = $aadhaarCardName;
            }

            if ($request->hasFile('pan_card')) {
                $panCard = $request->file('pan_card');
                $panCardName = 'pan-' . $currentTimestamp . '.' . $panCard->getClientOriginalExtension();
                $panCardDirectory = 'owners/' . $userId . '/documents';

                if (!Storage::disk('public')->exists($panCardDirectory)) {
                    Storage::disk('public')->makeDirectory($panCardDirectory);
                }

                $panCardPath = $panCard->storeAs($panCardDirectory, $panCardName, 'public');
                $document->pan_card = $panCardName;
            }

            $document->save();

            return response()->json(['message' => 'Documents uploaded successfully', 'document' => new OwnerResource($document)], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Owner not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while uploading documents', 'details' => $e->getMessage()], 500);
        }
    }

    public function upload_agreement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agreement'   => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'agreement_id' => 'required|integer',
            'property_id' => 'required|string',
            'approved'    => 'nullable|boolean',
            'remark'      => 'nullable|string|max:500',
            'signature_owner' => 'nullable|boolean',
            'agreement_url' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            $document =  Auth::guard('owner')->user();
            $validatedData = $validator->validated();
            $agreementName = null; // Default value in case no file is uploaded

            if ($request->hasFile('agreement')) {
                $agreement = $request->file('agreement');
                $currentTimestamp = Carbon::now()->format('Ymd_His');
                $agreementName = 'agreement-' . $currentTimestamp . '.' . $agreement->getClientOriginalExtension();
                $agreementPath = "property/{$validatedData['property_id']}/agreement/";

                // Create directory if it doesn't exist
                if (!Storage::disk('public')->exists($agreementPath)) {
                    Storage::disk('public')->makeDirectory($agreementPath);
                }

                // Store file
                $agreement->storeAs($agreementPath, $agreementName, 'public');
            } else {
                $lastOwnerLog = AgreementLog::where('property_id', $validatedData['property_id'])
                    ->where('agreement_id', $validatedData['agreement_id'])->where('owner_id', $document->id)
                    ->latest('created_at')
                    ->first();
                // dd($document->id);
                $agreementName = $lastOwnerLog->agreement;
            }
            $agreement = AgreementLog::create([
                'property_id' => $validatedData['property_id'],
                'owner_id'    => $document->id,
                'agreement_id' => $validatedData['agreement_id'] ?? null,
                'agreement'   => $agreementName,
                'highlight' => 0,
                'remark'      => $validatedData['remark'] ?? '',
                'description' => isset($validatedData['remark']) ? $validatedData['remark'] : 'Agreement upload by Owner.',
                'owner_approve'   => isset($validatedData['approved']) ? $validatedData['approved'] : 0,
                'signature_owner' => isset($validatedData['signature_owner']) ? $validatedData['signature_owner'] : 0,
                'created_at'  => now(),
            ]);
            return response()->json(['message' => 'Documents uploaded successfully', 'document' => $agreement], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while uploading documents', 'details' => $e->getMessage()], 500);
        }
    }

    public function uploadAgreementDetail(Request $request)
    {

        $validatedData = $request->validate([
            'property_id' => 'required|string',
            'rent' => 'required|numeric',
            'deposit' => 'required|numeric',
            'monthly_maintenance' => 'required|numeric',
            'contract_duration' => 'required|integer',
            'contract_renewal_increment' => 'required|string',
            'painting_deep_cleaning_charges' => 'required|numeric',
            'notice_period' => 'required|string',
            'agreement'   => 'nullable|file|mimes:pdf,doc,docx|max:2048', // PDF validation
        ]);

        try {
            // Get authenticated owner
            $owner = Auth::guard('owner')->user();
            // dd($owner);
            if (!$owner) {
                return response()->json(['error' => 'Owner not authenticated'], 401);
            }

            // Check if an agreement already exists for this property and owner
            $existingAgreement = AgreementDetail::where('property_id', $validatedData['property_id'])
                ->where('owner_id', $owner->id)
                ->exists();

            if ($existingAgreement) {
                return response()->json(['error' => 'Agreement already uploaded for this property'], 400);
            }


            // Handle file upload
            if ($request->hasFile('agreement')) {
                $agreement = $request->file('agreement');
                $sanitizedUserName = preg_replace('/[^A-Za-z0-9\-]/', '_', $owner->name);
                $timestamp = Carbon::now()->format('Ymd_His');
                $agreementName = "agreement-{$timestamp}.{$agreement->getClientOriginalExtension()}";

                $storagePath = "property/" . $validatedData['property_id'] . "/agreement/";

                // Ensure directory exists
                if (!Storage::disk('public')->exists($storagePath)) {
                    Storage::disk('public')->makeDirectory($storagePath);
                }

                // Store file
                $agreement->storeAs($storagePath, $agreementName, 'public');
            }

            // Save property details
            $agreement = AgreementDetail::create([
                'property_id' => $validatedData['property_id'],
                'rent' => $validatedData['rent'],
                'owner_id' => Auth::guard('owner')->id(),
                'deposit' => $validatedData['deposit'],
                'monthly_maintenance' => $validatedData['monthly_maintenance'],
                'contract_duration' => $validatedData['contract_duration'],
                'contract_renewal_increment' => $validatedData['contract_renewal_increment'],
                'painting_deep_cleaning_charges' => $validatedData['painting_deep_cleaning_charges'],
                'notice_period' => $validatedData['notice_period'],
            ]);
            if ($agreement) {
                AgreementLog::create([
                    'agreement_id' => $agreement->id,
                    'property_id' => $agreement->property_id,
                    'owner_id' => Auth::guard('owner')->id(),
                    'agreement' => $agreementName,
                    'remark' => '',
                    'description' => 'Agreement has been created successfully by owner.',
                    'created_at' => now(),
                ]);
            }
            return response()->json([
                'message' => 'agreement added successfully',
                'property' => $agreement,
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong', 'details' => $e->getMessage()], 500);
        }
    }

    public function getAgreement()
    {
        // Get the authenticated owner
        $ownerId = Auth::guard('owner')->id();
        // dd($owner);
        if (!$ownerId) {
            return response()->json(['message' => 'Owner not authenticated'], 401);
        }

        $latestAgreement = AgreementDetail::with(['properties', 'latestAgreementLogOwner'])
            ->whereHas('latestAgreementLogOwner', function ($query) use ($ownerId) {
                $query->where('owner_id', $ownerId); // Ensure latest log belongs to this owner
            })
            ->latest('created_at')
            ->get();
        if (!$latestAgreement) {
            return response()->json(['message' => 'No agreement found'], 404);
        }

        return response()->json([
            'message' => 'Latest agreement fetched successfully',
            'document' => $latestAgreement
        ], 200);
    }

    public function getNotification()
    {

        // dd();
        // $profile = Auth::guard('owner')->user();
        // $notifications = $profile->notifications;
        // dd($notifications);
        // return view('notification', compact('notifications'));
        $profile = Auth::guard('owner')->user();
        if ($profile) {
            // Fetch notifications for the user
            $notifications = Notifications::where('notifiable_id', $profile->id)
                ->get();
            // dd($notifications);
            return response()->json([
                'status' => true,
                'message' => 'Notifications retrieved successfully.',
                'data' => $notifications
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Profile not found.',
                'data' => null
            ], 404);
        }
    }

    public function markAsRead($id)
    {
        $notification = Notifications::findOrFail($id);
        $notification->read_at = now();
        $notification->save();
        $data = json_decode($notification->data, true);

        if (isset($data['property_id'])) {
            $propertyId = $data['property_id'];

            // Retrieve the property with owner relationship
            $propertyInfo = Property::with(['owner'])->findOrFail($propertyId);

            // Return the property info in the response
            return response()->json([
                'status' => true,
                'message' => 'Notifications retrieved successfully.',
                'data' => $propertyInfo
            ], 200);
        } else {
            // property_id is not set in the notification data
            return response()->json([
                'status' => false,
                'message' => 'Property ID not found in notification data.',
                'data' => null
            ], 400);
        }
    }
}
