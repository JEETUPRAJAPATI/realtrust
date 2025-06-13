<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ConformTiming;
use App\Models\FieldManager;
use App\Models\Owner;
use App\Models\Property;
use App\Models\ScheduleVisit;
use App\Models\WhatsappMessage;
use App\Models\WhatsappReply;
use App\Services\TwilioWhatsAppService;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class WhatsAppController extends Controller
{
    // protected $whatsappService;

    // public function __construct(TwilioWhatsAppService $whatsappService)
    // {
    //     $this->whatsappService = $whatsappService;
    // }

    // public function sendMessage(Request $request)
    // {
    //     $request->validate([
    //         'to' => 'required|string',
    //         'message' => 'required|string',
    //     ]);

    //     $this->whatsappService->sendMessage($request->to, $request->message);

    //     return response()->json(['message' => 'Message sent successfully']);
    // }

    // public function sendTemplateMessage(Request $request)
    // {
    //     $request->validate([
    //         'to' => 'required|string',
    //         'template_name' => 'required|string',
    //         'template_params' => 'required|array',
    //     ]);

    //     $this->whatsappService->sendTemplateMessage($request->to, $request->template_name, $request->template_params);

    //     return response()->json(['message' => 'Template message sent successfully']);
    // }

    // public function sendTimeConfirmation($ownerId)
    // {
    //     // Your Twilio credentials
    //     $sid = env('TWILIO_SID');
    //     $token = env('TWILIO_TOKEN');
    //     $whatsappFrom = env('TWILIO_WHATSAPP_FROM');
    //     $owner = Owner::findOrFail($ownerId); // Assuming you have an Owner model


    //     // Create a new Twilio client
    //     $client = new Client($sid, $token);

    //     // Prepare the confirmation message
    //     $messageBody = "Your Yummy Cupcakes Company order of 1 dozen frosted cupcakes has shipped and should be delivered on July 10, 2019. Details : http://www.yummycupcakes.com/";

    //     // Send the message
    //     $client->messages->create(
    //         "whatsapp:{$owner->mobile_no}", // Owner's WhatsApp number
    //         [
    //             'from' => "{$whatsappFrom}",
    //             'body' => $messageBody,
    //         ]
    //     );
    //     Toastr::success('message', 'Time confirmation message sent successfully!');
    //     return back();
    // }





    // whatsapp send template meta

    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function sendTimeConfirmation123($properties_id)
    {
        // dd($properties_id);
        $property = Property::with('owner')->where('unique_id', $properties_id)->firstOrFail();
        $phoneNumber = $property->owner->mobile_no;
        if (!str_starts_with($phoneNumber, '+91')) {
            $phoneNumber = '+91' . ltrim($phoneNumber, '0');
        }
        $propertyId =   $property->unique_id;
        $owner_id =   $property->owner_id;
        $confirmationUrl = $propertyId;
        $imageUrl = asset('storage/property/' . $property->owner_id . '/' . $property->unique_id . '/' . $property->image);

        $templateName = 'owner_confirmation_time';
        $languageCode = 'en_US';
        $variables = [
            $property->owner->name,
            $property->title,
            $property->locality . ', ' . $property->city,
            $property->bhk,
            $property->price
        ];
        // dd($variables);
        $response = $this->whatsAppService->sendImageTemplateMessage($phoneNumber, $templateName, $languageCode, $variables, $confirmationUrl, $imageUrl);
        // Handle the response
        if (isset($response['error']) && $response['error'] === true) {
            // Log failed message
            WhatsappMessage::create([
                'owner_id' => $property->owner_id,
                'unique_id' => $property->unique_id,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'variables' => $variables,
                'status' => 'failed',
                'api_response' => $response['message'],
            ]);
            // return response()->json(['error' => true, 'message' => 'Failed to send message: ' . $response['message']], 500);
            Toastr::error('Failed to send message: ' . $response['message'], 'Error');
        } else {
            // Log successful message
            WhatsappMessage::create([
                'owner_id' => $property->owner_id,
                'unique_id' => $property->unique_id,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'variables' => $variables,
                'message_id' => $response['messages'][0]['id'] ?? null, // Assuming response contains message ID
                'status' => 'sent',
                'api_response' => $response,
                'sent_at' => now(),
            ]);

            Toastr::success('Time confirmation message sent successfully to Owner!', 'Success');
        }
        return back();
    }
    public function sendTimeConfirmation($properties_id)
    {
        // dd($properties_id);
        $property = Property::with('owner')->where('unique_id', $properties_id)->firstOrFail();
        $phoneNumber = $property->owner->mobile_no;
        if (!str_starts_with($phoneNumber, '+91')) {
            $phoneNumber = '+91' . ltrim($phoneNumber, '0');
        }
        $propertyId =   $property->unique_id;
        $owner_id =   $property->owner_id;
        $confirmationUrl = $propertyId;
        $imageUrl = asset('storage/property/' . $property->owner_id . '/' . $property->unique_id . '/' . $property->image);

        $templateName = 'owner_confirmation_time';
        $languageCode = 'en_US';
        $variables = [
            $property->owner->name,
            $property->title,
            $property->locality . ', ' . $property->city,
            $property->bhk,
            $property->price
        ];
        // dd($variables);
        $response = $this->whatsAppService->sendImageTemplateMessage($phoneNumber, $templateName, $languageCode, $variables, $confirmationUrl, $imageUrl);
        // Handle the response
        if (isset($response['error']) && $response['error'] === true) {
            // Log failed message
            WhatsappMessage::create([
                'owner_id' => $property->owner_id,
                'unique_id' => $property->unique_id,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'variables' => $variables,
                'status' => 'failed',
                'api_response' => $response['message'],
            ]);
            // return response()->json(['error' => true, 'message' => 'Failed to send message: ' . $response['message']], 500);
            Toastr::error('Failed to send message: ' . $response['message'], 'Error');
        } else {
            // Log successful message
            WhatsappMessage::create([
                'owner_id' => $property->owner_id,
                'unique_id' => $property->unique_id,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'variables' => $variables,
                'message_id' => $response['messages'][0]['id'] ?? null, // Assuming response contains message ID
                'status' => 'sent',
                'api_response' => $response,
                'sent_at' => now(),
            ]);

            Toastr::success('Time confirmation message sent successfully to Owner!', 'Success');
        }
        return back();
    }
    public function sendTimeConfirmationFieldManager($field_manager_id, $property_id)
    {
        // dd($field_manager_id, $property_id);
        $result = ConformTiming::with('field_manager', 'properties')->where('field_manager_id', $field_manager_id)->where('property_id', $property_id)->first();

        if ($result->timing == null) {
            Toastr::error('First Time confirmation to Owner!', 'error');
            return back();
        }
        $phoneNumber = $result->field_manager->mobile_no;
        if (!str_starts_with($phoneNumber, '+91')) {
            $phoneNumber = '+91' . ltrim($phoneNumber, '0');
        }

        $confirmationUrl = $result->property_id;
        $imageUrl = asset('storage/property/' . $result->properties->owner_id . '/' . $result->properties->unique_id . '/' . $result->properties->image);

        $templateName = 'field_manager_confirmation';
        $languageCode = 'en';
        $variables = [
            $result->field_manager->name,
            $result->properties->title,
            $result->properties->locality . ', ' . $result->properties->city,
            $result->properties->bhk,
            $result->properties->price
        ];
        // dd($variables);
        $response = $this->whatsAppService->sendImageTemplateMessage($phoneNumber, $templateName, $languageCode, $variables, $confirmationUrl, $imageUrl);
        // Handle the response
        if (isset($response['error']) && $response['error'] === true) {
            // Log failed message
            WhatsappMessage::create([
                'field_manager_id' =>  $result->field_manager->id,
                'unique_id' => $result->property_id,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'variables' => $variables,
                'status' => 'failed',
                'api_response' => $response['message'],
            ]);
            Toastr::error('Failed to send message: ' . $response['message'], 'Error');
        } else {
            // Log successful message
            WhatsappMessage::create([
                'field_manager_id' =>  $result->field_manager->id,
                'unique_id' => $result->property_id,
                'phone_number' => $phoneNumber,
                'template_name' => $templateName,
                'variables' => $variables,
                'message_id' => $response['messages'][0]['id'] ?? null, // Assuming response contains message ID
                'status' => 'sent',
                'api_response' => $response,
                'sent_at' => now(),
            ]);

            Toastr::success('Time confirmation message sent successfully to Field Manager!', 'Success');
        }

        return back();
    }
    // public function sendTimeConfirmation($ownerId)
    // {

    //     $owner = Owner::findOrFail($ownerId); // Assuming you have an Owner model

    //     $phoneNumber = $owner->mobile_no;
    //     $templateName = 'hello_world';
    //     $languageCode = 'en_US'; // Example language code
    //     // $variables = [
    //     //     ['type' => 'text', 'text' => 'John Doe']
    //     // ];

    //     $response = $this->whatsAppService->sendTemplateMessage($phoneNumber, $templateName, $languageCode);
    //     return response()->json($response);
    // }



    // webhook
    public function handleIncomingMessage(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'from' => 'required|string',
            'messages' => 'required|array',
        ]);

        foreach ($request->messages as $message) {
            // Save the message details in the database
            WhatsappReply::create([
                'from' => $message['from'], // Sender's phone number
                'message' => $message['text'], // Message content
                'message_id' => $message['id'] ?? null, // Optional message ID
            ]);
        }

        return response()->json(['status' => 'success'], 200);
    }

    public function confirmTiming($property_id)
    {
        if ($property_id) {
            $visit = ConformTiming::where('property_id', $property_id)
                ->whereNotNull('timing')
                ->first();

            if ($visit) {
                [$startDatetime, $endDatetime] = explode(' - ', $visit->timing);

                // Format the start and end times using Carbon
                $formattedStart = Carbon::createFromFormat('m/d/Y h:i A', $startDatetime)->format('Y-m-d h:i A');
                $formattedEnd = Carbon::createFromFormat('m/d/Y h:i A', $endDatetime)->format('Y-m-d h:i A');
                return view('conform-time-success', ['message' => 'You have already confirmed your timing at ' .  $formattedStart . ' to ' . $formattedEnd]);
            } else {
                $visiterInfo = Property::with('owner')->where('unique_id', $property_id)->firstOrFail();
                // $visiterInfo = ConformTiming::with('field_manager', 'properties')->where('property_id', $property_id)->first();
                // dd($visiterInfo);
                return view('conform-time', compact('visiterInfo'));
            }
        }
    }

    public function confirmTimingFieldManager($property_id)
    {
        //  dd($property_id);
        if ($property_id) {
            $visit = ConformTiming::where('property_id', $property_id)->where('conform_timing', 1)->first();

            if ($visit) {
                [$startDatetime, $endDatetime] = explode(' - ', $visit->timing);

                // Format the start and end times using Carbon
                $formattedStart = Carbon::createFromFormat('m/d/Y h:i A', $startDatetime)->format('Y-m-d h:i A');
                $formattedEnd = Carbon::createFromFormat('m/d/Y h:i A', $endDatetime)->format('Y-m-d h:i A');
                return view('conform-time-success', ['message' => 'You have already confirmed your timing at ' . $formattedStart . ' to ' . $formattedEnd]);
            } else {
                // $visiterInfo = Property::with('owner')->where('unique_id', $property_id)->firstOrFail();
                $visiterInfo = ConformTiming::with('field_manager', 'properties.owner')->where('property_id', $property_id)->first();
    
                return view('conform-time', compact('visiterInfo'));
            }
        }
    }
    public function confirmTimingSubmit(Request $request)
    {
        // dd($request->all());
    
        $validator = Validator::make($request->all(), [
            'property_id' => 'required',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'title' => 'required|string|max:255',
            'flat_number' => 'required|max:255',
            'key_person_number' => 'nullable|digits:10',
            'date' => 'required|date',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',

        ]);
            
        $propertyId = $request->input('property_id');
        $visit = ConformTiming::where('property_id', $propertyId)->first();
        $date = $request->date; // 2025-04-26
        $startTime = $request->startTime; 
        $endTime = $request->endTime; 
        
        $startDateTime = \Carbon\Carbon::parse("$date $startTime");
        $endDateTime = \Carbon\Carbon::parse("$date $endTime");
        
        // Format the datetime into your required format
        $formattedTimeRange = $startDateTime->format('m/d/Y h:i A') . ' - ' . $endDateTime->format('m/d/Y h:i A');
        // Check if validation fails
        if ($validator->fails()) {
            // Return back with errors and old input
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        } else {
            
            $image = $request->file('gate_pass');
            $slug  = Str::slug($request->name);
    
            if (isset($image)) {
                $currentDate = Carbon::now()->toDateString();
                $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

                if (!Storage::disk('public')->exists('gate_pass')) {
                    Storage::disk('public')->makeDirectory('gate_pass');
                }

                $owner =  Image::make($image)->stream();
                Storage::disk('public')->put('gate_pass/' . $imagename, $owner);
            } else {
                $imagename = null;
            }

            $field_manager = $request->input('file_manager') ?? '';
            $conform_timing = $request->input('conform_timing') ?? '';
            
            // dd($visit);
            if (!empty($field_manager) &&  !empty($conform_timing)) {

                $visit->conform_timing = $conform_timing;
                $visit->save();

                return view('conform-time-success', ['message' => 'Your request has been processed successfully. Thank you! ']);
            }
            // $datetime = Carbon::createFromFormat('Y-m-d h:i A', $request->input('datetime'), 'UTC');
            $datetimeRange = $request->input('datetime'); 
            // dd($datetime);
            if (!$visit) {
                // Insert into the schedule_visits table
                ConformTiming::create([
                    'property_id' => $propertyId,
                    'timing' => $formattedTimeRange,
                    'gate_pass' => $imagename,
                    'key_person_number' => $request->key_person_number,
                    'flat_number' => $request->flat_number,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $visit->flat_number = $request->flat_number;
                $visit->gate_pass = $imagename;
                $visit->key_person_number = $request->key_person_number;
                $visit->timing = $formattedTimeRange;
                $visit->save();
            }
            return view('conform-time-success', ['message' => 'Your request has been processed successfully. Thank you! ']);
        }
    }
}
