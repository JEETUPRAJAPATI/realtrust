<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleVisitUserList;
use Illuminate\Http\Request;
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
use App\Models\User;
use App\Models\WhatsappMessage;
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
        $visiterInfo = ScheduleVisit::with('property', 'field_manager', 'owner', 'userLists.user')->get();
        // dd($visiterInfo);
        return view('admin.schedule_visit.index', compact('visiterInfo'));
    }

    public function create()
    {
        $property = Property::all();
        $fieldManager = FieldManager::all();
        $users = User::all();
        $owners = Owner::all();
        // dd($property, $fieldManager, $users);
        return view('admin.schedule_visit.create', compact('property', 'fieldManager', 'users', 'owners'));
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
            ->setTimezone('Asia/Kolkata')
            ->setTimezone('UTC');

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
    public function store(Request $request)
    {
        // dd($request->all());
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
        // dd('dsa');
        // $visit = ScheduleVisit::where([
        //     'property_id' => $request->input('properties'),
        //     'owner_id' => $request->input('owner'),
        //     'user_id' => $request->input('users')
        // ])->first();
        // $datetime = Carbon::createFromFormat('Y-m-d H:i:s', trim($request->input('timing')))->setTimezone('Asia/Kolkata')->setTimezone('UTC');
        // $datetime = Carbon::createFromFormat('Y-m-d\TH:i:se', $request->input('datetime'), 'UTC');
        // dd($datetime);
        // $datetime = Carbon::createFromFormat('Y-m-d h:i A', trim($request->input('timing')), 'Asia/Kolkata')
        // ->setTimezone('UTC');
        $datetime = Carbon::createFromFormat('Y-m-d h:i A', $request->input('timing'), 'UTC');

        // dd($datetime);
        // 'timing' => Carbon::createFromFormat('Y-m-d h:i A', $request->input('timing'))->format('Y-m-d H:i:s'),
        $visit = ScheduleVisit::create([
            'property_id' => $request->input('properties'),
            'field_manager_id' => $request->input('field_manager_id'),
            'owner_id' => $request->input('owner'),
            'staff_id' => Auth::guard('admin')->user()->id,
            'timing' => $datetime,
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
        }


        return redirect()->route('admin.schedule_visit.index');


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

    public function sendTemplateUser($propertyId)
    {
        $visit = ScheduleVisit::with([
            'property',
            'field_manager',
            'owner',
            'userLists.user',
            'conform_timing',
        ])->where('property_id', $propertyId)->firstOrFail();


        foreach ($visit->userLists as $detail) {
            $user = $detail->user; // Access the related user object directly

            // Check if the user exists before proceeding
            if ($user) {
                try {
                    // Send WhatsApp message to the user
                    $this->sendWhatsAppMessageToUser($visit, $user);
                } catch (\Exception $e) {
                    // Log any errors for debugging
                    Log::error("Failed to send WhatsApp message to User ID {$user->id}: " . $e->getMessage());
                }
            }
        }
        return redirect()->route('admin.schedule_visit.index');
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
        // dd($visiterInfo);
        // $fieldManager = FieldManager::all();
        return view('admin.schedule_visit.view', compact('visiterInfo'));
    }
    public function sendWhatsAppMessageToFieldManager($visit)
    {

        $scheduleVisit = ScheduleVisit::with('user', 'field_manager', 'property')->findOrFail($visit->id);

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
            $scheduleVisit->property->locality . ', ' . $scheduleVisit->property->city,
        ];

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

    private function sendWhatsAppMessageToUser($visit, $user)
    {
        $scheduleVisit = ScheduleVisit::with('user', 'field_manager', 'property')->findOrFail($visit->id);
        // dd($scheduleVisit);
        // $phoneNumber = $scheduleVisit->user->mobile_no;

        $localityName = Locality::where('id', $scheduleVisit->property->locality)->first()->name ?? 'No locality found';
        $societyName = Society::where('id', $scheduleVisit->property->society_name)->first()->name ?? 'No society found';
        $cityName = Cities::where('city_id', $scheduleVisit->property->city)->first()->city_name ?? 'No city found';

        $imageUrl = asset('storage/property/' . $scheduleVisit->property->owner_id . '/' . $scheduleVisit->property->unique_id . '/' . $scheduleVisit->property->image);

        $userInfo = ScheduleVisitUserList::with('user')->findOrFail($user->id);
        $phoneNumber = $userInfo->user->mobile_no;
        if (!str_starts_with($phoneNumber, '+91')) {
            $phoneNumber = '+91' . ltrim($phoneNumber, '0');
        }
        $templateName = 'property_schedule_confirmation_user'; // Define your template name here
        $languageCode = 'en_US';
        $confirmationUrl =  $scheduleVisit->field_manager->id;
        
        $callingUrl = $user->user_id  . '/' . $scheduleVisit->field_manager->id;
        $variables = [
            $userInfo->user->name,
            $scheduleVisit->property->title,
            $scheduleVisit->timing,
            $scheduleVisit->property->locality . ', ' . $scheduleVisit->property->city,
            $scheduleVisit->field_manager->mobile_no,
        ];
        // dd($variables);
        $response = $this->whatsAppService->sendingWhatsAppMessageToUser($phoneNumber, $templateName, $languageCode, $variables, $confirmationUrl,$imageUrl,$$callingUrl);


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


    public function edit($id)
    {
        $visiterInfo = ScheduleVisit::with('property', 'user', 'field_manager', 'owner')->findOrFail($id);
        $properties = Property::all();
        $fieldManagers = FieldManager::all();
        $users = User::all();
        $owners = Owner::all();
        return view('admin.schedule_visit.edit', compact('visiterInfo', 'properties', 'fieldManagers', 'users', 'owners'));
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
        return redirect()->route('admin.schedule_visit.index');
    }
    public function destroy(string $id)
    {
        $ScheduleVisit = ScheduleVisit::find($id);
        // dd($ScheduleVisit);
        $ScheduleVisit->delete();
        Toastr::success('message', 'Schedule visit deleted successfully.');
        return back();
    }
    public function unreadCount()
    {

        $unreadCount = Auth::guard('admin')->user()->unreadNotifications->count();
        return response()->json(['count' => $unreadCount]);
    }
    public function userList($id)
    {
        $visiterInfo = ScheduleVisitUserList::with('visit', 'user')
            ->where('visite_id', $id)
            ->get();
        // dd($visiterInfo);

        return view('admin.schedule_visit.user', compact('visiterInfo'));
    }
    // public function markAsRead($id)
    // {
    //     $notification = Notifications::findOrFail($id);
    //     $notification->read_at = now();
    //     $notification->save();
    //     $data = json_decode($notification->data, true);
    //     if (isset($data['schedule_visit_id'])) {
    //         $scheduleVisitId = $data['schedule_visit_id'] ?? null;
    //         $visiterInfo = ScheduleProperties::with(['property.owner'])
    //             ->findOrFail($scheduleVisitId);
    //         // dd($visiterInfo->property->owner);
    //         return view('staff.schedule_visit.show', compact('visiterInfo', 'data'));
    //     } elseif (isset($data['property_id'])) {
    //         $property_id = $data['property_id'] ?? null;
    //         $property = Property::with(['owner', 'comments'])->withCount('comments')->where('unique_id', $property_id)->firstOrFail();
    //         return view('staff.properties.show', compact('property'));
    //     }
    // }


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
    public function showEmailForm($id)
    {
        $owners = Owner::find($id);
        return view('admin.owner.emails.send_mail', compact('owners'));
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
}
