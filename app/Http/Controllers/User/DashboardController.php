<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\AgreementDetail;
use App\Models\AgreementLog;
use App\Models\Owner;
use App\Models\User;
use App\Models\ScheduleProperties;
use App\Models\UserDocument;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    //
    public function index()
    {

        $id = Auth::guard('user')->user()->id;

        $owners = Owner::with('properties')->get();
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
            ], 404);
        }

        return response()->json([
            'message' => 'Properties list retrieved successfully.',
            'counts' => $counts
        ], 200);
    }


    public function profile()
    {
        $profile = Auth::guard('user')->user();

        // Check if a profile exists
        if ($profile) {
            return response()->json([
                'status' => true,
                'message' => 'Profile Data retrieved successfully.',
                'data' => new UserResource($profile) // Transform user data using UserResource
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Profile not found.',
                'data' => null // No data to return since the profile doesn't exist
            ], 404);
        }
    }
    public function profileUpdate(Request $request)
    {

        $profile = Auth::guard('user')->user();
        if (!$profile) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized owner.'
            ], 404);
        }

        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                Rule::unique('users', 'email')->ignore($profile->id)
            ],
            'mobile_no' => [
                'required',
                'numeric',
                'digits_between:10,15',
                Rule::unique('users', 'mobile_no')->ignore($profile->id)
            ],
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,avif|max:2048' // Max size 2MB
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
        $image = $request->file('image');
        
        if ($request->hasFile('image')) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('users')) {
                Storage::disk('public')->makeDirectory('users');
            }
            if (Storage::disk('public')->exists('users/' . $profile->image)) {
                Storage::disk('public')->delete('users/' . $profile->image);
            }
            $userimg = Image::make($image)->stream();
            Storage::disk('public')->put('users/' . $imagename, $userimg);
        } else {
            $imagename = $profile->image;
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
            'data' => new UserResource($profile)
        ], 200);
    }


    public function uploadDocuments1(Request $request)
    {
        
        $request->validate([
            'company_name' => 'required|string|max:255',
            'employee_id' => 'required|string|max:255',
            'aadhaar_card' => 'file|mimes:pdf,jpeg,png,jpg|max:2048',
            'pan_card' => 'file|mimes:pdf,jpeg,png,jpg|max:2048',
        ]);

        try {
            $document = Auth::guard('user')->user();
            $document->company_name = $request->company_name;
            $document->employee_id = $request->employee_id;
            // $userName = preg_replace('/[^A-Za-z0-9\-]/', '_', $document->name); // sanitize the user name to avoid invalid characters in file names
            $currentTimestamp = Carbon::now()->format('Ymd_His');
            $userId = $document->id; // Get the user ID

            if ($request->hasFile('aadhaar_card')) {
                $aadhaarCard = $request->file('aadhaar_card');
                $aadhaarCardName = 'aadhaar-' . $currentTimestamp . '.' . $aadhaarCard->getClientOriginalExtension();
                $aadhaarCardDirectory = 'users/' . $userId . '/documents';

                if (!Storage::disk('public')->exists($aadhaarCardDirectory)) {
                    Storage::disk('public')->makeDirectory($aadhaarCardDirectory);
                }
                $aadhaarCardPath = $aadhaarCard->storeAs($aadhaarCardDirectory, $aadhaarCardName, 'public');
                $document->aadhaar_card = $aadhaarCardName;
            }

            if ($request->hasFile('pan_card')) {
                $panCard = $request->file('pan_card');
                $panCardName = 'pan-' . $currentTimestamp . '.' . $panCard->getClientOriginalExtension();
                $panCardDirectory = 'users/' . $userId . '/documents';

                if (!Storage::disk('public')->exists($panCardDirectory)) {
                    Storage::disk('public')->makeDirectory($panCardDirectory);
                }

                $panCardPath = $panCard->storeAs($panCardDirectory, $panCardName, 'public');
                $document->pan_card = $panCardName;
            }

            $document->save();

            return response()->json(['message' => 'Documents uploaded successfully', 'document' => new UserResource($document)], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while uploading documents', 'details' => $e->getMessage()], 500);
        }
    }
    public function getDocumentsList()
    {
        $user = Auth::guard('user')->user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $documentsItem = User::with('userDocuments')->where('id', $user->id)->first();

        if (!$documentsItem) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'message' => 'Documents fetched successfully',
            'document' => new UserResource($documentsItem)
        ], 200);
    }


    public function uploadDocuments(Request $request)
    {
    
        $request->validate([
            'company_name' => 'required|string|max:255',
            'employee_id' => 'required|string|max:255',
            'aadhaar_card' => 'file|mimes:pdf,jpeg,png,jpg,webp,avif|max:2048',
            'pan_card' => 'file|mimes:pdf,jpeg,png,jpg,webp,avif|max:2048',
        ]);


        try {
            $document = new UserDocument;
            $document->user_id = Auth::guard('user')->user()->id;
            $document->company_name = $request->company_name;
            $document->employee_id = $request->employee_id;
            // $userName = preg_replace('/[^A-Za-z0-9\-]/', '_', $document->name); // sanitize the user name to avoid invalid characters in file names
            $currentTimestamp = Carbon::now()->format('Ymd_His');
            $userId = Auth::guard('user')->user()->id; // Get the user ID

            if ($request->hasFile('aadhaar_card')) {
                $aadhaarCard = $request->file('aadhaar_card');
                $aadhaarCardName = 'aadhaar-' . $currentTimestamp . '.' . $aadhaarCard->getClientOriginalExtension();
                $aadhaarCardDirectory = 'users/' . $userId . '/documents';

                if (!Storage::disk('public')->exists($aadhaarCardDirectory)) {
                    Storage::disk('public')->makeDirectory($aadhaarCardDirectory);
                }
                $aadhaarCardPath = $aadhaarCard->storeAs($aadhaarCardDirectory, $aadhaarCardName, 'public');
                $document->aadhaar_card = $aadhaarCardName;
            }

            if ($request->hasFile('pan_card')) {
                $panCard = $request->file('pan_card');
                $panCardName = 'pan-' . $currentTimestamp . '.' . $panCard->getClientOriginalExtension();
                $panCardDirectory = 'users/' . $userId . '/documents';

                if (!Storage::disk('public')->exists($panCardDirectory)) {
                    Storage::disk('public')->makeDirectory($panCardDirectory);
                }

                $panCardPath = $panCard->storeAs($panCardDirectory, $panCardName, 'public');
                $document->pan_card = $panCardName;
            }


             $document->save();
            $documentsItem = User::with('userDocuments')->where('id', Auth::guard('user')->user()->id)->first();
            return response()->json([
                'message' => 'Documents uploaded successfully',
                'document' => new UserResource($documentsItem)
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while uploading documents', 'details' => $e->getMessage()], 500);
        }
    }

    public function uploadDocumentsUpdate(Request $request)
    {

        $request->validate([
            'company_name' => 'required|string|max:255',
            'employee_id' => 'required|string|max:255',
            'aadhaar_card' => 'file|mimes:pdf,jpeg,png,jpg,webp,avif|max:2048',
            'pan_card' => 'file|mimes:pdf,jpeg,png,jpg,webp,avif|max:2048',
        ]);

        try {
            // Find the existing document for the authenticated user
            $document = UserDocument::where('id', $request->id)->firstOrFail();

            $userId = Auth::guard('user')->user()->id;
            $document->company_name = $request->company_name;
            $document->employee_id = $request->employee_id;
            $currentTimestamp = Carbon::now()->format('Ymd_His');
            $documentDirectory = "users/{$userId}/documents";

            // Update Aadhaar Card
            if ($request->hasFile('aadhaar_card')) {
                // Delete old Aadhaar Card file if it exists
                if (!empty($document->aadhaar_card)) {
                    $oldAadhaarPath = "{$documentDirectory}/{$document->aadhaar_card}";
                    if (Storage::disk('public')->exists($oldAadhaarPath)) {
                        Storage::disk('public')->delete($oldAadhaarPath);
                    }
                }

                $aadhaarCard = $request->file('aadhaar_card');
                $aadhaarCardName = "aadhaar-{$currentTimestamp}." . $aadhaarCard->getClientOriginalExtension();
                $aadhaarCardPath = $aadhaarCard->storeAs($documentDirectory, $aadhaarCardName, 'public');

                $document->aadhaar_card = $aadhaarCardName;
            }

            // Update PAN Card
            if ($request->hasFile('pan_card')) {
                // Delete old PAN Card file if it exists
                if (!empty($document->pan_card)) {
                    $oldPanPath = "{$documentDirectory}/{$document->pan_card}";
                    if (Storage::disk('public')->exists($oldPanPath)) {
                        Storage::disk('public')->delete($oldPanPath);
                    }
                }

                $panCard = $request->file('pan_card');
                $panCardName = "pan-{$currentTimestamp}." . $panCard->getClientOriginalExtension();
                $panCardPath = $panCard->storeAs($documentDirectory, $panCardName, 'public');
                $document->pan_card = $panCardName;
            }

            // Save the updated document
            $document->save();

            // Retrieve updated user documents
            $documentsItem = User::with('userDocuments')->where('id', $userId)->first();

            return response()->json([
                'message' => 'Documents updated successfully',
                'document' => new UserResource($documentsItem)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Document not found'], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while updating documents',
                'details' => $e->getMessage()
            ], 500);
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
            'signature_user'      => 'nullable|boolean',
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
            $user = Auth::guard('user')->user();
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
                $lastUserLog = AgreementLog::where('property_id', $validatedData['property_id'])
                    ->where('agreement_id', $validatedData['agreement_id'])->where('user_id', $user->id)
                    ->latest('created_at')
                    ->first();
                $agreementName = $lastUserLog->agreement;
            }
            $agreement = AgreementLog::create([
                'property_id' => $validatedData['property_id'],
                'user_id'    => $user->id,
                'agreement'   => $agreementName,
                'agreement_id' => $validatedData['agreement_id'] ?? null,
                'highlight' => 0,
                'remark'      => $validatedData['remark'] ?? '',
                'description' => isset($validatedData['remark']) ? $validatedData['remark'] : 'Agreement upload by User.',
                'user_approve'   => isset($validatedData['approved']) ? $validatedData['approved'] : 0,
                'signature_user' => isset($validatedData['signature_user']) ? $validatedData['signature_user'] : 0,
                'created_at'  => now(),
            ]);
            return response()->json(['message' => 'Documents uploaded successfully', 'document' => $agreement], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while uploading documents', 'details' => $e->getMessage()], 500);
        }
    }

    public function getAgreement()
    {
        // Get the authenticated owner
        $userId = Auth::guard('user')->id();
        // dd($user);
        if (!$userId) {
            return response()->json(['message' => 'user not authenticated'], 401);
        }

        // Fetch latest agreement for the given property and user
        $latestAgreement = AgreementDetail::with(['properties', 'latestAgreementLogUser'])
            ->whereHas('latestAgreementLogUser', function ($query) use ($userId) {
                $query->where('user_id', $userId); // Ensure latest log belongs to this owner
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
    public function deleteDocuments(Request $request)
    {

        try {
            // Validate request
            $request->validate([
                'id' => 'required|exists:user_documents,id',
            ]);

            // Find the document
            $document = UserDocument::where('id', $request->id)->firstOrFail();
            $userId = Auth::guard('user')->user()->id;
            $documentDirectory = "users/{$userId}/documents";

            // Delete Aadhaar Card file if it exists
            if (!empty($document->aadhaar_card)) {
                $aadhaarPath = "{$documentDirectory}/{$document->aadhaar_card}";
                if (Storage::disk('public')->exists($aadhaarPath)) {
                    Storage::disk('public')->delete($aadhaarPath);
                }
            }

            // Delete PAN Card file if it exists
            if (!empty($document->pan_card)) {
                $panPath = "{$documentDirectory}/{$document->pan_card}";
                if (Storage::disk('public')->exists($panPath)) {
                    Storage::disk('public')->delete($panPath);
                }
            }

            // Delete document record from database
            $document->delete();

            return response()->json([
                'message' => 'Documents deleted successfully'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Document not found'], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while deleting documents',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    public function historyScheduleProperties(Request $request)
    {

        $user = Auth::guard('user')->user();

        $property = ScheduleProperties::where('email', $user->email)->get();
        if ($property) {
            return response()->json([
                'status' => true,
                'message' => 'Properties Data retrieved successfully.',
                'data' => $property
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Profile not found.',
                'data' => null
            ], 404);
        }
    }

    public function getSchedulePropertyTiming()
    {
        $user = Auth::guard('user')->user();

        $property = ScheduleProperties::where('email', $user->email)->get();
        if ($property) {
            return response()->json([
                'status' => true,
                'message' => 'Properties Data retrieved successfully.',
                'data' => $property
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Profile not found.',
                'data' => null
            ], 404);
        }
    }
}
