<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Mail\ScheduleVisitMail;
use App\Models\Cities;
use App\Models\ConformTiming;
use App\Models\EmailLog;
use App\Models\FieldManager;
use App\Models\Locality;
use App\Models\Owner;
use App\Models\Property;
use App\Models\ScheduleProperties;
use App\Models\ScheduleVisit;
use App\Models\ScheduleVisitUserList;
use App\Models\User;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Notification;
use App\Models\Notifications;
use App\Models\Society;
use App\Rules\ConformTimingRule;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Support\Str;

class ScheduleVisitController extends Controller
{


    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }
    public function index()
    {
        
        $visiterInfo = ScheduleVisit::with('property', 'field_manager', 'owner', 'userLists.user')->orderBy('id', 'desc')->get();

        // $visiterInfo = ScheduleVisit::select('property_id', 'owner_id', \DB::raw('MIN(timing) as timing'))
        // ->with(['property', 'owner'])
        // ->groupBy('property_id', 'owner_id')
        // ->get();

        // dd($visiterInfo);
        return view('staff.schedule_visit.index', compact('visiterInfo'));
    }

    public function create()
    {
        $property = Property::all();
        $fieldManager = FieldManager::all();
        $users = User::all();
        $owners = Owner::all();
        // dd($property, $fieldManager, $users);
        return view('staff.schedule_visit.create', compact('property', 'fieldManager', 'users', 'owners'));
    }

    public function getOwnersByProperty(Request $request)
    {
        $unique_id = $request->input('propertyId');
        // Assuming Property model has a relation with Owner
        $property = Property::with('owner')->where('unique_id', $unique_id)->first();

        // dd($property);
        if ($property) {
            $owners = $property->owner;
            return response()->json($owners);
        }

        return response()->json([]);
    }


    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'properties' => 'required',
            'field_manager_id' => 'required',
            'users' => 'required',
            'owner' => 'required',
            'timing' => 'required',
        ]);

        // Step 4: Redirect back if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Step 2: Prepare the timing with seconds and convert to UTC
        $timingWithSeconds = $request->input('timing') . ':00';
    
        $datetime = Carbon::createFromFormat('Y-m-d H:i:s', trim($timingWithSeconds))
            ->setTimezone('Asia/Kolkata');

        // Step 3: Start a database transaction
        DB::beginTransaction();

        // Step 4: Create the ConformTiming record
        $conformTiming = ConformTiming::create([
            'property_id' => $request->input('properties'),
            'field_manager_id' => $request->input('field_manager_id'),
            'timing' => $datetime,
            'status' => 1,
        ]);

        // Step 5: Create the ScheduleVisit record
        $visit = ScheduleVisit::create([
            'property_id' => $request->input('properties'),
            'field_manager_id' => $request->input('field_manager_id'),
            'owner_id' => $request->input('owner'),
            'staff_id' => Auth::guard('staff')->user()->id,
            'timing' => $datetime,
            'status' => 'sending',
        ]);

        // Step 6: Create ScheduleVisitUserList records for each user

        $user = ScheduleVisitUserList::create([
            'user_id' => $request->input('users'),
            'visite_id' => $visit->id
        ]);


        // Step 7: Commit the transaction
        DB::commit();

        // Step 8: Send WhatsApp messages
        $this->sendWhatsAppMessageToFieldManager($visit);
        $this->sendWhatsAppMessageToUser($visit, $user);

        return redirect()->route('staff.schedule_visit.index')->with('success', 'Schedule Visit successfully created.');
    }
    
    public function manual_schedule_visit(Request $request)
    {
         // dd($request->all());
     
     // Step 1: Validate request data
        $validator = Validator::make($request->all(), [
            'properties' => 'required|string',
            'owner'      => 'required|integer',
            'users'      => 'required|integer',
            'visit_type' => 'nullable|string',
            'company'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Step 2: Fetch property by unique ID
            $property = Property::with('owner')->where('unique_id', $request->input('properties'))->first();

            if (!$property) {
                return response()->json([
                    'status' => false,
                    'message' => 'Property not found.',
                ], 404);
            }

            // Step 3: Check if a schedule already exists for the property
            $existingSchedule = ScheduleProperties::where('property_id', $property->id)->first();
            if ($existingSchedule) {
                return response()->json([
                    'status' => false,
                    'message' => 'This property visit is already scheduled. Once timing confirms, the join button will appear. Please wait...',
                ], 409); 
            }

            // Step 4: Fetch user details
            $user = User::find($request->input('users'));
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.',
                ], 404);
            }
           // dd($user);
            // Step 5: Create schedule record
            $scheduledVisit = ScheduleProperties::create([
                'property_id'  =>$request->input('properties'),
                'full_name'    => $user->name,
                'email'        => $user->email,
                'visit_type'   => $request->input('visit_type'),
                'company_name' => $request->input('company'),
            ]);

                 return redirect()->route('staff.schedule_properties.index')
            ->with('success', 'Scheduled visit created successfully.');
        } catch (\Exception $e) {
            // Step 6: Handle unexpected exceptions
               return redirect()->back()
            ->with('error', 'An unexpected error occurred: ' . $e->getMessage())
            ->withInput();
        }
     
    }
    public function store(Request $request)
    {
       
     
        $validator = Validator::make($request->all(), [
            'properties' => 'required',
            // 'field_manager_id' => 'required',
            'users' => 'required',
            'owner' => 'required',
            'timing' => [new ConformTimingRule()],
        ]);

        // Step 4: Redirect back if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //  dd('dsa');
        // $visit = ScheduleVisit::where([
        //     'property_id' => $request->input('properties'),
        //     'owner_id' => $request->input('owner'),
        //     'user_id' => $request->input('users')
        // ])->first();
        // $datetime = Carbon::createFromFormat('Y-m-d H:i:s', trim($request->input('timing')))->setTimezone('Asia/Kolkata')->setTimezone('UTC');
        // $datetime = Carbon::createFromFormat('Y-m-d h:i A', trim($request->input('timing')), 'Asia/Kolkata');
        // dd($datetime);
        $visit = ScheduleVisit::create([
            'property_id' => $request->input('properties'),
            'field_manager_id' => $request->input('field_manager_id'),
            'owner_id' => $request->input('owner'),
            'staff_id' => Auth::guard('staff')->user()->id,
            'timing' => $request->input('timing'),
            'status' => 'sending'
        ]);
        $user = ScheduleVisitUserList::create([
            'user_id' => $request->input('users'),
            'visite_id' => $visit->id
        ]);


        if ($visit) {
            // Send WhatsApp message to the field manager
            $this->sendWhatsAppMessageToFieldManager($visit);
            // Send WhatsApp message to the User
            $this->sendWhatsAppMessageToUser($visit, $user);

            $scheduleProperty = ScheduleProperties::findOrFail($request->input('id'));
            $scheduleProperty->status = 'schedule';
            $scheduleProperty->save();
            
            // Notify all waiting users
            // $waitingUsers = ScheduleWaitingList::where('property_id', $request->property_id)
            //     ->where('status', 'waiting')
            //     ->get();
            // foreach ($waitingUsers as $waitingUser) {
            //     $this->notifyWaitingUser($waitingUser, $ScheduleVisit);
            //     $waitingUser->status = 'notified';
            //     $waitingUser->save();
            // }
        }


        return redirect()->route('staff.schedule_visit.index');


        // $user = $request->input('users');
        // $field_manager_id = $request->input('field_manager_id');
        // $timing = $request->input('timing');

        // // Schedule email
        // Mail::to('jeetu.radicalloop@gmail.com')->send(new ScheduleVisitMail($user, $field_manager_id, $timing));

        // Log email if using logging
        // EmailLog::create([
        //     'owner_name' => $ownerName,
        //     'mail_body' => $mailBody,
        //     'sent_at' => $timing
        // ]);
    }

    public function sendWhatsAppMessageToFieldManager($visit)
    {

        $scheduleVisit = ScheduleVisit::with('user', 'field_manager', 'property')->findOrFail($visit->id);

        $localityName = Locality::where('id', $scheduleVisit->property->locality)->first()->name ?? 'No locality found';
        $societyName = Society::where('id', $scheduleVisit->property->society_name)->first()->name ?? 'No society found';
        $cityName = Cities::where('city_id', $scheduleVisit->property->city)->first()->city_name ?? 'No city found';

        $gatPassDetails = ConformTiming::where('property_id', $scheduleVisit->property->unique_id)->first();
        $gatePass = $gatPassDetails->gate_pass ?? null;
        $flatNumber = $gatPassDetails->flat_number ?? null;


        $phoneNumber = $scheduleVisit->field_manager->mobile_no;
        if (!str_starts_with($phoneNumber, '+91')) {
            $phoneNumber = '+91' . ltrim($phoneNumber, '0');
        }
        $templateName = 'property_schedule_conform_field_manager'; // Define your template name here
        $languageCode = 'en';
        $variables = [
            $scheduleVisit->field_manager->name,
            $scheduleVisit->property->title,
            $scheduleVisit->timing,
            $cityName . ',' . $localityName . ',' . $societyName,
               $gatePass,
            $flatNumber
        ];
        // $scheduleVisit->property->locality . ', ' . $scheduleVisit->property->city,

        // dd($variables);
        $response = $this->whatsAppService->sendingWhatsAppMessageToFieldManager($phoneNumber, $templateName, $languageCode, $variables);
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
            Toastr::success('Message sent to Field Manager successfully.', 'Success');
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



    public function edit($id)
    {
        $visiterInfo = ScheduleVisit::with('property', 'user', 'field_manager', 'owner')->findOrFail($id);
        $properties = Property::all();
        $fieldManagers = FieldManager::all();
        $users = User::all();
        $owners = Owner::all();
        return view('staff.schedule_visit.edit', compact('visiterInfo', 'properties', 'fieldManagers', 'users', 'owners'));
    }
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'field_manager_id' => 'required',
            'timing' => 'required|date'
        ]);
        // Check if validation fails
        if ($validator->fails()) {
            // Return back with errors and old input
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $visit = ScheduleVisit::where('id', $id)->firstOrFail();
        $visit->field_manager_id = $request->input('field_manager_id');
        $visit->timing = Carbon::createFromFormat('Y-m-d h:i A', $request->input('timing'))->setTimezone('Asia/Kolkata')->setTimezone('UTC');
        $visit->save();

        Toastr::success('message', 'Schedule visit successfully.');
        return redirect()->route('staff.schedule_visit.index');
    }
    public function destroy(string $id)
    {
        $ScheduleVisit = ScheduleVisit::find($id);
        if ($ScheduleVisit) {
            $ScheduleVisit->delete();
            ScheduleVisitUserList::where('visite_id', $id)->delete();
            Toastr::success('Schedule visit deleted successfully.', 'Success');
        } else {
            Toastr::error('Schedule visit not found.', 'Error');
        }
        return back();
    }
    public function unreadCount()
    {

        $unreadCount = Auth::guard('staff')->user()->unreadNotifications->count();
        return response()->json(['count' => $unreadCount]);
    }

    public function view($scheduleVisitId)
    {
        // $visiterInfo = ScheduleProperties::with(['property.owner', 'users', 'conform_timing', 'schedule_visit_date.field_manager'])->where('property_id', $scheduleVisitId)->firstOrFail();
        $visiterInfo = ScheduleVisit::with([
            'property',
            'field_manager',
            'owner',
            'userLists.user',
            'conform_timing',
        ])
            ->where('property_id', $scheduleVisitId)
            ->firstOrFail();
        dd($visiterInfo);
        // $fieldManager = FieldManager::all();
        return view('staff.schedule_visit.view', compact('visiterInfo'));
    }
    public function userList($id)
    {
        $visiterInfo = ScheduleVisitUserList::with('visit', 'user')
            ->where('visite_id', $id)
            ->get();
        // dd($visiterInfo);

        return view('staff.schedule_visit.user', compact('visiterInfo'));
    }
    public function markAsRead($id)
    {
        $notification = Notifications::findOrFail($id);
        $notification->read_at = now();
        $notification->save();
        $data = json_decode($notification->data, true);
        if (isset($data['schedule_visit_id'])) {
            $scheduleVisitId = $data['schedule_visit_id'] ?? null;
            $visiterInfo = ScheduleProperties::with(['property.owner'])
                ->findOrFail($scheduleVisitId);
            // dd($visiterInfo->property->owner);
            return view('staff.schedule_visit.show', compact('visiterInfo', 'data'));
        } elseif (isset($data['property_id'])) {
            $property_id = $data['property_id'] ?? null;
            $property = Property::with(['owner', 'comments'])->withCount('comments')->where('unique_id', $property_id)->firstOrFail();
            return view('staff.properties.show', compact('property'));
        }
    }


    // public function scheduleVisit($id)
    // {
    //     // $scheduleVisitId = $id ?? null;
    //     // $visiterInfo = ScheduleProperties::with(['property.owner'])->where('property_id', $scheduleVisitId)->first();
    //     // dd($visiterInfo->property->owner);
    //     $visiterInfo = ScheduleProperties::with(['property.owner', 'conform_timing'])->where('property_id', $id)->firstOrFail();
    //     // $fieldManager = FieldManager::all();
    //     // $users = User::all();
    //     // dd($visiterInfo);
    //     // return view('staff.schedule_visit.show', compact('visiterInfo', 'fieldManager', 'users'));
    //     // return view('staff.schedule_visit.show', compact('visiterInfo'));


    //     $property = Property::all();
    //     $fieldManager = FieldManager::all();
    //     $users = User::all();
    //     $owners = Owner::all();
    //     // dd($property, $fieldManager, $users);
    //     return view('staff.schedule_visit.show', compact('visiterInfo', 'property', 'fieldManager', 'users', 'owners'));
    // }


    private function sendWhatsAppMessageToUser($visit, $user)
    {
        $scheduleVisit = ScheduleVisit::with('user', 'field_manager', 'property')->findOrFail($visit->id);

        $localityName = Locality::where('id', $scheduleVisit->property->locality)->first()->name ?? 'No locality found';
        $societyName = Society::where('id', $scheduleVisit->property->society_name)->first()->name ?? 'No society found';
        $cityName = Cities::where('city_id', $scheduleVisit->property->city)->first()->city_name ?? 'No city found';

        $imageUrl = asset('storage/property/' . $scheduleVisit->property->owner_id . '/' . $scheduleVisit->property->unique_id . '/' . $scheduleVisit->property->image);

         $gatPassDetails = ConformTiming::where('property_id', $scheduleVisit->property->unique_id)->first();
         
        // $gatePass = $gatPassDetails->gate_pass ?? null;
        
        $gatePassUrl = asset(Storage::url('gate_pass/' . $gatPassDetails->gate_pass));
        
        $flatNumber = $gatPassDetails->flat_number ?? null;
        // $propertyImage
        // $phoneNumber = $scheduleVisit->user->mobile_no;
        $userInfo = ScheduleVisitUserList::with('user')->findOrFail($user->id);
        $phoneNumber = $userInfo->user->mobile_no;
        if (!str_starts_with($phoneNumber, '+91')) {
            $phoneNumber = '+91' . ltrim($phoneNumber, '0');
        }
        $templateName = 'property_schedule_confirmation_user'; // Define your template name here
        
        $languageCode = 'en_US';
        $confirmationUrl =  $scheduleVisit->field_manager->id;
        
        $callingUrl = $user->user_id . '/' . $scheduleVisit->field_manager->id;
        
        $variables = [
            $userInfo->user->name,
            $scheduleVisit->property->title,
            $scheduleVisit->timing,
            $cityName . ',' . $localityName . ',' . $societyName,
            $scheduleVisit->property->bhk,
        ];
        // dd($variables);
        $response = $this->whatsAppService->sendingWhatsAppMessageToUser($phoneNumber, $templateName, $languageCode, $variables, $confirmationUrl, $imageUrl,$callingUrl);


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




    public function sendConformationTemplateUser($propertyId)
    {
        $scheduleVisit = ScheduleVisit::with('property')->where('property_id', $propertyId)->first();
        if (!$scheduleVisit) {
            Log::error('Schedule visit not found', ['property_id' => $propertyId]);
            Toastr::error('Schedule visit not found for the property.', 'Error');
            return;
        }

        $localityName = Locality::find($scheduleVisit->property->locality)->name ?? 'No locality found';
        $societyName = Society::find($scheduleVisit->property->society_name)->name ?? 'No society found';
        // $cityName = Cities::find($scheduleVisit->property->city)->city_name ?? 'No city found';
        $imageUrl = asset('storage/property/' . $scheduleVisit->property->owner_id . '/' . $scheduleVisit->property->unique_id . '/' . $scheduleVisit->property->image);

        $scheduleVisitUserList = ScheduleVisitUserList::with('visit', 'user')->where('visite_id', $scheduleVisit->id)->get();
        if ($scheduleVisitUserList->isEmpty()) {
            Log::error('No users found for the scheduled visit', ['schedule_visit_id' => $scheduleVisit->id]);
            Toastr::error('No users found for the scheduled visit.', 'Error');
            return;
        }

        // Variables to track success and failure
        $successCount = 0;
        $failureCount = 0;
        $failedUsers = [];

        foreach ($scheduleVisitUserList as $userInfo) {
            $phoneNumber = $userInfo->user->mobile_no;
            if (!str_starts_with($phoneNumber, '+91')) {
                $phoneNumber = '+91' . ltrim($phoneNumber, '0');
            }

            $templateName = 'user_conformation_form';
            $languageCode = 'en';
            $variables = [
                $userInfo->user->name,
                $scheduleVisit->property->title,
                $scheduleVisit->timing,
                "{$localityName}, {$societyName}"
            ];

            Log::info('Preparing to send WhatsApp template message:', [
                'phone_number' => $phoneNumber,
                'variables' => $variables,
            ]);
            $confirmationUrl = $scheduleVisit->property->unique_id . '#payments';
            $callingUrl = $userInfo->user_id . '/' . $scheduleVisit->staff_id;
          
            $response = $this->whatsAppService->sendConformationForm(
                $phoneNumber,
                $templateName,
                $languageCode,
                $variables,
                $confirmationUrl,
                $imageUrl,
                $callingUrl
            );
            if (isset($response['error']) && $response['error']) {
                $failureCount++;
                $failedUsers[] = $userInfo->user->name;
                Log::error("Failed to send WhatsApp message", ['response' => $response]);
            } else {
                $successCount++;
                Log::info("WhatsApp message sent successfully", ['response' => $response]);
            }

            // Log message to store API response
            WhatsappMessage::create([
                'unique_id' => $scheduleVisit->property->unique_id,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'variables' => json_encode($variables),
                'message_id' => $response['message_id'] ?? null,
                'status' => isset($response['error']) && $response['error'] ? 'failed' : 'sent',
                'api_response' => json_encode($response),
                'sent_at' => now(),
            ]);
        }
        // After processing all users, show a single success or error message
        if ($failureCount > 0) {
            // Show error if there were any failures
            Toastr::error("Failed to send message to {$failureCount} user(s): " . implode(", ", $failedUsers), 'Error');
        } else {
            // Show success if all messages were sent successfully
            Toastr::success("Message sent to {$successCount} user(s) successfully.", 'Success');
        }
        return back();
    }
}
