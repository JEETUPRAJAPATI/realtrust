<?php

namespace App\Http\Controllers\User;

use App\Events\PropertyVisitScheduled as EventsPropertyVisitScheduled;
use App\Http\Controllers\Controller;
use App\Models\ConformTiming;
use App\Models\Property;
use App\Models\ScheduleProperties;
use App\Models\ScheduleVisit;
use App\Models\ScheduleVisitUserList;
use App\Models\ScheduleWaitingList;
use App\Models\Staff;
use App\Models\User;
use App\Models\WhatsappMessage;
use App\Notifications\PropertyVisitScheduled;
use App\Services\InteraktWhatsAppService;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Locality;
use App\Models\Cities;
use App\Models\Society;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yoeunes\Toastr\Facades\Toastr;

class ScheduleVisitController extends Controller
{


    protected $InteraktWhatsAppService;

    public function __construct(InteraktWhatsAppService $InteraktWhatsAppService)
    {
        $this->InteraktWhatsAppService = $InteraktWhatsAppService;
    }

    public function scheduleVisit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required',
            'full_name' => 'required',
            'email' => 'required',
            'visit_type' => 'required',
            'company' => 'required',
            'timing' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if (!$request->timing) {
                $existingPropertySchedule = ScheduleProperties::where('property_id', $request->input('property_id'))
                    ->first();
                if ($existingPropertySchedule) {

                    $ScheduleWaitingList = ScheduleWaitingList::updateOrCreate([
                        'property_id' => $request->property_id,
                        'email' => $request->email,
                    ], [
                        'status' => 'waiting'
                    ]);

                    $this->sendCongratsMessageUser($ScheduleWaitingList);

                    return response()->json([
                        'status' => false,
                        'message' => 'This property visit has already been scheduled. You will be notified once the visit timing is confirmed. Please wait...',
                    ], 404);
                }
            }

            $existingSchedule = ScheduleProperties::where('property_id', $request->input('property_id'))
                ->where('email', $request->input('email'))->where('status', 'sending')
                ->first();
            // dd($existingSchedule);

            if ($existingSchedule) {

                $visit = ConformTiming::where('property_id', $request->input('property_id'))
                    // ->where('timing', $request->input('timing'))
                    ->where('conform_timing', 1)
                    ->first();
                if (!$visit) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Please confirm the timing with the owner after join.',
                        'data' => $visit
                    ], 404);
                }
                return response()->json([
                    'status' => false,
                    'message' => 'This property visit is already scheduled.',
                    'data' => $existingSchedule
                ], 404);
            }
            if ($request->input('timing')) {

                $beforAnySchedule = ScheduleProperties::where('property_id', $request->input('property_id'))
                    ->where('email', $request->input('email'))->where('status', 'schedule')
                    ->first();

                if (!empty($beforAnySchedule)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Alredy join this property.',
                        'data' => $beforAnySchedule
                    ], 404);
                }

                // Check if the timing is confirmed with the owner
                $existingSchedule = ScheduleVisit::where('property_id', $request->input('property_id'))
                    ->where('timing', $request->input('timing'))->where('status', 'sending')
                    ->first();

                if (!$existingSchedule) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Please confirm the timing with the owner after join.',
                        'data' => $existingSchedule
                    ], 404);
                }
            }

            $existingSchedule = ScheduleProperties::where('property_id', $request->input('property_id'))->first();
            $item = null;

            if (!$existingSchedule) {
                $item = ScheduleProperties::firstOrCreate(
                    [
                        'property_id' => $request->input('property_id'),
                        'email' => $request->input('email'),
                        'visit_type' => $request->input('visit_type'),
                    ],
                    [
                        'full_name' => $request->input('full_name'),
                        'company_name' => $request->input('company'),
                    ]
                );

                if (!$item->wasRecentlyCreated) {
                    return response()->json([
                        'status' => false,
                        'message' => 'You have already scheduled this visit.',
                        'data' => $item,
                    ], 409);
                }
            }

            if ($existingSchedule || $item) {
                // Retrieve the property and user
                $property = Property::with('owner')->where('unique_id', $request->input('property_id'))->first();

                if (!$property) {
                    return response()->json(['status' => false, 'message' => 'Property not found'], 404);
                }

                $user = Auth::guard('user')->user();
                $staffMembers = Staff::all(); // Get all staff members


                if ($request->input('timing')) {
                    // $datetime = Carbon::createFromFormat('Y-m-d H:i:s', trim($request->input('timing')))->setTimezone('Asia/Kolkata')->setTimezone('UTC');

                    // // Create a record in the ScheduleVisit table
                    // $scheduleVisit = ScheduleVisit::create([
                    //     'owner_id' => $property->owner->id, // Assuming 'owner' is the correct relation name
                    //     'user_id' => $user->id,
                    //     'property_id' => $request->input('property_id'),
                    //     'field_manager_id' => $existingSchedule->field_manager_id, // Ensure this field exists
                    //     'timing' => $datetime, // You might want to adjust what 'timing' should be
                    //     'status' => 'sending',
                    //     'otp_verification' => 'pending', // Assuming 'otp_verification' needs to be set
                    // ]);

                    $scheduleVisit = ScheduleVisit::where('property_id', $request->input('property_id'))->where('timing', $request->input('timing'))->first();
                    if (!$scheduleVisit) {
                        return response()->json(['status' => false, 'message' => 'Schedule visit not found for the provided timing.'], 404);
                    }
                    // Create a record in the ScheduleVisit table
                    try {
                        $alreadyJoined = ScheduleVisitUserList::where('visite_id', $scheduleVisit->id)
                            ->where('user_id', $user->id)
                            ->exists();

                        if ($alreadyJoined) {
                            return response()->json([
                                'status' => false,
                                'message' => 'You have already joined this scheduled visit.'
                            ], 409); // Conflict status
                        }
                        $scheduleVisitUser = ScheduleVisitUserList::create([
                            'visite_id' => $scheduleVisit->id,
                            'user_id' => $user->id,
                            'otp_verification' => 'pending',
                        ]);
                        if ($scheduleVisitUser) {
                            $this->sendWhatsAppMessageToFieldManager($scheduleVisit);
                            $this->sendJoinPendingUserScheduleVisit($scheduleVisit);

                            $scheduleProperty = ScheduleProperties::find($item ? $item->id : $existingSchedule->id);
                            if ($scheduleProperty) {
                                $scheduleProperty->status = 'schedule';
                                $scheduleProperty->save();
                            }

                            return response()->json([
                                'status' => true,
                                'message' => 'Scheduled visit joined successfully.',
                                'result' => $scheduleVisit
                            ], 201);
                        }
                    } catch (Exception $e) {
                        Log::error('Failed to create ScheduleVisitUserList entry.', [
                            'error' => $e->getMessage(),
                            'schedule_visit_id' => $scheduleVisit->id,
                            'user_id' => $user->id,
                        ]);

                        return response()->json([
                            'status' => false,
                            'message' => 'Failed to join scheduled visit. Please try again later.'
                        ], 422);
                    }
                }
                foreach ($staffMembers as $staff) {
                    $staff->notify(new PropertyVisitScheduled($property, $user, $item));
                }

                $notification = $staff->notifications()->latest()->first();
                $notificationId = $notification->id;
                broadcast(new EventsPropertyVisitScheduled($property, $user, $item, $notificationId));
                $this->sendCongratsMessageOwner($item);
                return response()->json([
                    'status' => true,
                    'message' => 'Scheduled visit created successfully.',
                    'result' => $item
                ], 201);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong, please try again.'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function joinScheduleVisit(Request $request)
    {
        //  dd($request->all());
        $validator = Validator::make($request->all(), [
            'property_id' => 'required',
            'full_name' => 'required',
            'email' => 'required',
            'visit_type' => 'required',
            'company' => 'required',
            'timing' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        // dd($request->all());
        try {
            // Check if a visit is already scheduled for this property and email
            $existingScheduleProperty = ScheduleProperties::where('property_id', $request->input('property_id'))
                ->where('email', $request->input('email'))
                ->first();

            if ($existingScheduleProperty) {
                return response()->json([
                    'status' => false,
                    'message' => 'This property visit is already scheduled.',
                    'data' => $existingScheduleProperty
                ], 404);
            }

            // Check if the timing is confirmed with the owner
            $existingSchedule = ConformTiming::where('property_id', $request->input('property_id'))
                ->where('timing', $request->input('timing'))
                ->where('conform_timing', 1) // Ensure that the timing is confirmed by the owner
                ->first();

            if (!$existingSchedule) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please confirm the timing with the owner before scheduling the visit.',
                    'data' => $existingSchedule
                ], 404);
            }

            // Create the scheduled visit entry in ScheduleProperties table
            $item = ScheduleProperties::create([
                'property_id' => $request->input('property_id'),
                'full_name' => $request->input('full_name'),
                'email' => $request->input('email'),
                'visit_type' => $request->input('visit_type'),
                'company_name' => $request->input('company'),
            ]);
            // dd($item->id);
            if ($item) {
                // Retrieve the property and user details
                $property = Property::with('owner')->where('unique_id', $request->input('property_id'))->first();

                if (!$property) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Property not found',
                    ], 404);
                }

                // Get the authenticated user
                $user = Auth::guard('user')->user();

                $datetime = Carbon::createFromFormat('Y-m-d H:i:s', trim($request->input('timing')))->setTimezone('Asia/Kolkata')->setTimezone('UTC');

                // Create a record in the ScheduleVisit table
                $scheduleVisit = ScheduleVisit::create([
                    'owner_id' => $property->owner->id, // Assuming 'owner' is the correct relation name
                    'user_id' => $user->id,
                    'property_id' => $request->input('property_id'),
                    'field_manager_id' => $existingSchedule->field_manager_id, // Ensure this field exists
                    'timing' => $datetime, // You might want to adjust what 'timing' should be
                    'status' => 'sending',
                    'otp_verification' => 'pending', // Assuming 'otp_verification' needs to be set
                ]);


                if ($scheduleVisit) {
                    // Send WhatsApp message to the field manager
                    $this->sendWhatsAppMessageToFieldManager($scheduleVisit);
                    // Send WhatsApp message to the User
                    $this->sendWhatsAppMessageToUser($scheduleVisit);

                    $scheduleProperty = ScheduleProperties::findOrFail($item->id);
                    $scheduleProperty->status = 'schedule';
                    $scheduleProperty->save();
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Scheduled visit successfully.',
                    'result' => $scheduleVisit
                ], 201);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong, please try again.'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }



    public function sendWhatsAppMessageToFieldManager($visit)
    {

        $scheduleVisit = ScheduleVisit::with('user', 'field_manager', 'property')->findOrFail($visit->id);
        $gatPassDetails = ConformTiming::where('property_id', $scheduleVisit->property->unique_id)->first();


        $gatePass = $gatPassDetails->gate_pass ?? null;

        if ($gatePass && Storage::disk('public')->exists('gate_pass/' . $gatePass)) {
            $gatePassUrl = str_replace('/public', '', asset('storage/gate_pass/' . $gatePass));
        } else {
            $gatePassUrl = "Gate pass not available";
        }

        $flatNumber = $gatPassDetails->flat_number ?? 'Not available';

        $localityName = Locality::where('id', $scheduleVisit->property->locality)->first()->name ?? 'No locality found';
        $societyName = Society::where('id', $scheduleVisit->property->society_name)->first()->name ?? 'No society found';
        $cityName = Cities::where('city_id', $scheduleVisit->property->city)->first()->city_name ?? 'No city found';

        $phoneNumber = $scheduleVisit->field_manager->mobile_no;
        if (!str_starts_with($phoneNumber, '+91')) {
            $phoneNumber = '+91' . ltrim($phoneNumber, '0');
        }
        $templateName = 'notify_field_manager_to_visit'; // Define your template name here
        $languageCode = 'en';
        $variables = [
            $scheduleVisit->field_manager->name,
            $scheduleVisit->property->title,
            $scheduleVisit->timing,
            "$cityName, $localityName, $societyName",
            $gatePassUrl,
            $flatNumber
        ];

        // dd($variables);
        $callingUrl = $scheduleVisit->field_manager->id . '/' . $scheduleVisit->staff_id;

        // $response = $this->whatsAppService->sendingWhatsAppMessageToFieldManager($phoneNumber, $templateName, $languageCode, $variables);

        $response = $this->InteraktWhatsAppService->sendNotifyFieldManagerToVisit(
            $phoneNumber,
            $scheduleVisit->field_manager->name,
            $scheduleVisit->property->title,
            $scheduleVisit->timing,
            "$cityName, $localityName, $societyName",
            $gatePassUrl,
            $flatNumber,
            $callingUrl
        );
        if (isset($response['error']) && $response['error'] === true) {
            // Log failed message
            WhatsappMessage::create([
                'unique_id' => $scheduleVisit->property->unique_id,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'variables' => $variables,
                'status' => 'failed',
                'api_response' => $response['message'],
            ]);
            Toastr::error('Message sent failed to Field Manager successfully.', 'Success');
            return;
            // return response()->json(['error' => true, 'message' => 'Failed to send message: ' . $response['message']], 500);
        } else {
            // Log successful message
            WhatsappMessage::create([

                'unique_id' => $scheduleVisit->property->unique_id,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'variables' => $variables,
                'message_id' => $response['messages'][0]['id'] ?? null, // Assuming response contains message ID
                'status' => 'sent',
                'api_response' => $response,
                'sent_at' => now(),
            ]);

            Toastr::success('Message sent to Field Manager successfully.', 'Success');
            return;
        }
    }

    private function sendJoinPendingUserScheduleVisit($visit)
    {
        $scheduleVisit = ScheduleVisit::with('user', 'field_manager', 'property')->findOrFail($visit->id);

        $localityName = Locality::where('id', $scheduleVisit->property->locality)->first()->name ?? 'No locality found';
        $societyName = Society::where('id', $scheduleVisit->property->society_name)->first()->name ?? 'No society found';
        $cityName = Cities::where('city_id', $scheduleVisit->property->city)->first()->city_name ?? 'No city found';

        // dd($scheduleVisit);
        $user = Auth::guard('user')->user();
        $phoneNumber = $user->mobile_no;
        if (!str_starts_with($phoneNumber, '+91')) {
            $phoneNumber = '+91' . ltrim($phoneNumber, '0');
        }
        $templateName = 'join_pending_user_schedule_visit'; // Define your template name here
        $languageCode = 'en_US';
        $confirmationUrl =  $scheduleVisit->field_manager->id;
        $callingUrl = $user->user_id  . '/' . $scheduleVisit->field_manager->id;
        $imageUrl = asset('storage/property/' . $scheduleVisit->property->owner_id . '/' . $scheduleVisit->property->unique_id . '/' . $scheduleVisit->property->image);


        $gatPassDetails = ConformTiming::where('property_id', $scheduleVisit->property->unique_id)->first();

        $gatePass = $gatPassDetails->gate_pass ?? null;

        if ($gatePass && Storage::disk('public')->exists('gate_pass/' . $gatePass)) {
            $gatePassUrl = str_replace('/public', '', asset('storage/gate_pass/' . $gatePass));
        } else {
            $gatePassUrl = "Gate pass not available";
        }

        $flatNumber = $gatPassDetails->flat_number ?? 'Not available';


        $variables = [
            $user->name,
            $scheduleVisit->property->title,
            $scheduleVisit->timing,
            $gatePassUrl,
            $flatNumber
        ];
        // dd($variables);
        $response = $this->InteraktWhatsAppService->sendJoinPendingUserScheduleVisit(
            $phoneNumber,
            $user->name,
            $scheduleVisit->property->title,
            $scheduleVisit->timing,
            "$cityName, $localityName, $societyName",
            $scheduleVisit->field_manager->mobile_no,
            $confirmationUrl,
            $callingUrl
        );

        if (isset($response['error']) && $response['error'] === true) {
            // Log failed message
            WhatsappMessage::create([
                'unique_id' => $scheduleVisit->property->unique_id,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'variables' => $variables,
                'message_id' => $response['messages'][0]['id'] ?? null, // Assuming response contains message ID
                'status' => 'sent',
                'api_response' => $response,
                'sent_at' => now(),
            ]);
            Toastr::error('Failed to send message to User: ' . $response['message'], 'Error');
            return;
            // return response()->json(['error' => true, 'message' => 'Failed to send message: ' . $response['message']], 500);
        } else {
            // Log successful message
            WhatsappMessage::create([
                'unique_id' => $scheduleVisit->property->unique_id,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'variables' => $variables,
                'message_id' => $response['messages'][0]['id'] ?? null, // Assuming response contains message ID
                'status' => 'sent',
                'api_response' => $response,
                'sent_at' => now(),
            ]);

            Toastr::success('Message sent to User successfully.', 'Success');
            return;
        }
    }


    private function sendWhatsAppMessageToUser($visit)
    {
        $scheduleVisit = ScheduleVisit::with('user', 'field_manager', 'property')->findOrFail($visit->id);
        // dd($scheduleVisit);
        $user = Auth::guard('user')->user();
        $phoneNumber = $user->mobile_no;
        if (!str_starts_with($phoneNumber, '+91')) {
            $phoneNumber = '+91' . ltrim($phoneNumber, '0');
        }
        $templateName = 'property_schedule_confirmation_user'; // Define your template name here
        $languageCode = 'en_US';
        $confirmationUrl =  $scheduleVisit->field_manager->id;
        $callingUrl = $user->user_id  . '/' . $scheduleVisit->field_manager->id;
        $imageUrl = asset('storage/property/' . $scheduleVisit->property->owner_id . '/' . $scheduleVisit->property->unique_id . '/' . $scheduleVisit->property->image);


        $gatPassDetails = ConformTiming::where('property_id', $scheduleVisit->property->unique_id)->first();

        $gatePass = $gatPassDetails->gate_pass ?? null;

        if ($gatePass && Storage::disk('public')->exists('gate_pass/' . $gatePass)) {
            $gatePassUrl = str_replace('/public', '', asset('storage/gate_pass/' . $gatePass));
        } else {
            $gatePassUrl = "Gate pass not available";
        }

        $flatNumber = $gatPassDetails->flat_number ?? 'Not available';

        $variables = [
            $user->name,
            $scheduleVisit->property->title,
            $scheduleVisit->timing,

            $gatePassUrl,
            $flatNumber
        ];
        // dd($variables);
        // $response = $this->whatsAppService->sendingWhatsAppMessageToUser($phoneNumber, $templateName, $languageCode, $variables, $confirmationUrl, $imageUrl, $callingUrl);

        $response = $this->InteraktWhatsAppService->sendVisitConfirmUser(
            $phoneNumber,
            $user->name,
            $scheduleVisit->property->title,
            $scheduleVisit->property->bhk,
            $scheduleVisit->timing,
            $scheduleVisit->property->locality . ', ' . $scheduleVisit->property->city,
            $scheduleVisit->field_manager->mobile_no,
            $$confirmationUrl,
            $callingUrl
        );

        if (isset($response['error']) && $response['error'] === true) {
            // Log failed message
            WhatsappMessage::create([
                'unique_id' => $scheduleVisit->property->unique_id,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'variables' => $variables,
                'message_id' => $response['messages'][0]['id'] ?? null, // Assuming response contains message ID
                'status' => 'sent',
                'api_response' => $response,
                'sent_at' => now(),
            ]);
            Toastr::error('Failed to send message to User: ' . $response['message'], 'Error');
            return;
            // return response()->json(['error' => true, 'message' => 'Failed to send message: ' . $response['message']], 500);
        } else {
            // Log successful message
            WhatsappMessage::create([
                'unique_id' => $scheduleVisit->property->unique_id,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'variables' => $variables,
                'message_id' => $response['messages'][0]['id'] ?? null, // Assuming response contains message ID
                'status' => 'sent',
                'api_response' => $response,
                'sent_at' => now(),
            ]);

            Toastr::success('Message sent to User successfully.', 'Success');
            return;
        }
    }

    private function sendCongratsMessageUser($visit)
    {
        Log::info('property', ['property is'  => $visit]);

        $result = ScheduleWaitingList::with('user', 'property')->where('property_id', $visit->property_id)->where('email', $visit->email)->firstOrFail();
        Log::info('property', ['property is'  => $result]);

        $localityName = Locality::where('id', $result->property->locality)->first()->name ?? 'No locality found';
        $societyName = Society::where('id', $result->property->society_name)->first()->name ?? 'No society found';
        $cityName = Cities::where('city_id', $result->property->city)->first()->city_name ?? 'No city found';

        // dd($result);
        $phoneNumber = $result->user->mobile_no;
        $templateName = 'schedule_visit_user';
        $languageCode = 'en';
        $variables = [
            $visit->full_name
        ];
        $userName = $result->user->name;
        $propertyName = $result->property->title ?? 'N/A';
        // dd($variables);
        // $response = $this->whatsAppService->sendCongratsMessageOwner($phoneNumber, $templateName, $languageCode, $variables);

        $response = $this->InteraktWhatsAppService->sendScheduleVisitUser($phoneNumber, $userName, $propertyName, "$cityName, $localityName, $societyName");

        if (isset($response['error']) && $response['error'] === true) {
            WhatsappMessage::create([
                'unique_id' => $visit->property_id,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'variables' => $variables,
                'message_id' => $response['messages'][0]['id'] ?? null,
                'status' => 'sent',
                'api_response' => $response,
                'sent_at' => now(),
            ]);
            Toastr::error('Failed to send message to User: ' . $response['message'], 'Error');
            return;
        } else {
            WhatsappMessage::create([
                'unique_id' => $visit->property_id,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'variables' => $variables,
                'message_id' => $response['messages'][0]['id'] ?? null,
                'status' => 'sent',
                'api_response' => $response,
                'sent_at' => now(),
            ]);
            Toastr::success('Message sent to User successfully.', 'Success');
            return;
        }
    }
    private function sendCongratsMessageOwner($visit)
    {
        $result = ScheduleProperties::with('users')->where('property_id', $visit->property_id)->where('email', $visit->email)->firstOrFail();
        $localityName = Locality::where('id', $result->property->locality)->first()->name ?? 'No locality found';
        $societyName = Society::where('id', $result->property->society_name)->first()->name ?? 'No society found';
        $cityName = Cities::where('city_id', $result->property->city)->first()->city_name ?? 'No city found';

        // dd($result);
        $phoneNumber = $result->users->mobile_no;
        // if (!str_starts_with($phoneNumber, '+91')) {
        //     $phoneNumber = ltrim($phoneNumber, '0');
        // }
        $templateName = 'schedule_visit_user';
        $languageCode = 'en';
        $variables = [
            $visit->full_name
        ];
        $userName = $result->users->name;
        $propertyName = $result->property->title ?? 'N/A';
        $location = $visit->location ?? 'N/A';
        // dd($variables);
        // $response = $this->whatsAppService->sendCongratsMessageOwner($phoneNumber, $templateName, $languageCode, $variables);

        $response = $this->InteraktWhatsAppService->sendScheduleVisitUser($phoneNumber, $userName, $propertyName, "$cityName, $localityName, $societyName");

        if (isset($response['error']) && $response['error'] === true) {
            WhatsappMessage::create([
                'unique_id' => $visit->property_id,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'variables' => $variables,
                'message_id' => $response['messages'][0]['id'] ?? null,
                'status' => 'sent',
                'api_response' => $response,
                'sent_at' => now(),
            ]);
            Toastr::error('Failed to send message to User: ' . $response['message'], 'Error');
            return;
        } else {
            WhatsappMessage::create([
                'unique_id' => $visit->property_id,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'variables' => $variables,
                'message_id' => $response['messages'][0]['id'] ?? null,
                'status' => 'sent',
                'api_response' => $response,
                'sent_at' => now(),
            ]);
            Toastr::success('Message sent to User successfully.', 'Success');
            return;
        }
    }
}
