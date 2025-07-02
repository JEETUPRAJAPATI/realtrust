<?php

namespace App\Http\Controllers\FieldManager;

use App\Http\Controllers\Controller;
use App\Models\ScheduleProperties;
use App\Models\ScheduleVisit;
use App\Models\ScheduleVisitUserList;
use App\Models\VerificationOtp;
use App\Models\WhatsappMessage;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yoeunes\Toastr\Facades\Toastr;
use App\Mail\ScheduleVisitMail;
use App\Models\Cities;
use App\Models\ConformTiming;
use App\Models\EmailLog;
use App\Models\FieldManager;
use App\Models\Locality;
use App\Models\Owner;
use App\Models\Property;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Notification;
use App\Models\Notifications;
use App\Models\Society;
use App\Rules\ConformTimingRule;
use App\Services\InteraktWhatsAppService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class VisiterController extends Controller
{

    protected $InteraktWhatsAppService;

    public function __construct(InteraktWhatsAppService $InteraktWhatsAppService)
    {
        $this->InteraktWhatsAppService = $InteraktWhatsAppService;
    }

    public function index()
    {
        $id = Auth::guard('field_manager')->user()->id;

        $field_managers = ScheduleVisit::with('property', 'user', 'owner')->where('field_manager_id', $id)->orderBy('id', 'desc')->get();
        return view('field_manager.visiter.index', compact('field_managers'));
    }

    public function userList($id)
    {
        $visiterInfo = ScheduleVisitUserList::with('visit', 'user')
            ->where('visite_id', $id)
            ->get();
        // dd($visiterInfo);

        return view('field_manager.visiter.user', compact('visiterInfo'));
    }
    public function edit($id)
    {
        $field_manager = ScheduleVisitUserList::with('property', 'user')->find($id);
        return view('field_manager.visiter.edit', compact('field_manager'));
    }

    public function sendOtpMessage(Request $request)
    {
        $id = Auth::guard('field_manager')->user()->id;
        $otp = $this->generateOtp($id, $request->type);

        $response = $this->sendOtp($request->mobile_no, $otp);
        // Check the response from the sendOtp function
        if (isset($response['error']) && $response['error'] === true) {
            return response()->json([
                'message' => 'Failed to send OTP to user: ' . $response['message'],
                'error_code' => $response['error_code'] ?? 'unknown',
            ], 500);
        }
        return response()->json(['message' => 'OTP sent successfully to user']);
    }
    function sendOtp($phone, $otp)
    {

        if (!str_starts_with($phone, '+91')) {
            $phone = '+91' . ltrim($phone, '0');
        }
        $templateName = 'otp_verification_message';
        $variables = $otp;
        // $response = $this->whatsAppService->sendOtpTemplateMessage($phone, $templateName, $languageCode, $variables);
        $response = $this->InteraktWhatsAppService->sendOtpVerificationMessage($phone, $otp);

        if (isset($response['error']) && $response['error'] === true) {
            WhatsappMessage::create([
                'phone_number' => $phone,
                'template_name' => $templateName,
                'variables' => json_encode($variables),
                'status' => 'failed',
                'api_response' => $response['message'],
            ]);

            // Log the error for debugging purposes
            Log::error('Failed to send WhatsApp message. Error: ' . $response['message']);

            // Optionally, you can also return a more descriptive error response to the client
            return [
                'error' => true,
                'message' => 'Failed to send message. ' . $response['message'],
                'error_code' => $response['error_code'] ?? 'unknown',
            ];
        } else {
            WhatsappMessage::create([
                'phone_number' => $phone,
                'template_name' => $templateName,
                'variables' => json_encode($variables),
                'message_id' => $response['messages'][0]['id'] ?? null,
                'status' => 'sent',
                'api_response' => json_encode($response),
                'sent_at' => now(),
            ]);

            return [
                'error' => false,
                'message' => 'OTP verification message sent successfully!'
            ];
        }
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'otp' => 'required|numeric', // Validate the OTP
        ]);
        $user_id = Auth::guard('field_manager')->user()->id;
        $verificationResponse = $this->verifyOtpUser($user_id, $request->otp, 'field_manager');
        if (!$verificationResponse) {
            return redirect()->back()->withInput();
        }
        $scheduleVisit = ScheduleVisitUserList::find($id);

        $property = ScheduleVisit::where('id', $scheduleVisit->visite_id)->select('property_id')->first();

        if (!$scheduleVisit) {
            Toastr::error('Schedule visit not found.', 'Error');
            return redirect()->route('field_manager.visiter.index');
        }
        if ($request->otp) {
            $scheduleVisit->otp_verification = 'done';
        }
        if ($scheduleVisit->save()) {

            $property_id = $property->property_id;
            $user_id = $scheduleVisit->user_id;
            // Retrieve user's email
            $user = User::where('id', $user_id)->select('email')->first();

            if (!$user) {
                return redirect()->route('field_manager.visiter.index')
                    ->with('error', 'Something went wrong. Please try again.');
            }

            $email = $user->email;
            $ScheduleProperties = ScheduleProperties::where('property_id', $property_id)->where('email', $email)->where('status', 'pending')->first();

            if ($ScheduleProperties) {
                // return redirect()->route('field_manager.visiter.index')
                //     ->with('error', 'Schedule property not found.');
                $ScheduleProperties->status = 'schedule';
                $ScheduleProperties->save();
            }
            if ($property_id && $user_id) {
                $this->sendConformationTemplateUser($property_id, $user_id);
                Toastr::success('OTP verification completed successfully.', 'Success');
            } else {
                Toastr::error('Failed to update schedule status. Please try again.', 'Error');
            }
        } else {
            Toastr::error('Failed to OTP varification. Please try again.', 'Error');
        }
        return redirect()->route('field_manager.visiter.index');
    }


    function verifyOtpUser($entityId, $inputOtp, $type)
    {

        $otpRecord = VerificationOtp::where($type . '_id', $entityId)
            ->where('otp', $inputOtp)
            ->where('is_used', false)->first();
        if (!$otpRecord) {
            return ['status' => false, 'message' => 'Invalid OTP.'];
        }
        if (Carbon::now()->greaterThan($otpRecord->expires_at)) {
            return ['status' => false, 'message' => 'OTP expired.'];
        }
        $otpRecord->update(attributes: ['is_used' => true]);
        return true;
    }
    function generateOtp($entityId, $type)
    {
        $otp = rand(100000, 999999);

        VerificationOtp::create([
            $type . '_id' => $entityId,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        return $otp;
    }
    public function view($id)
    {
        $property_id = $id ?? null;
        $scheduleVisit = ScheduleVisitUserList::with('property', 'user')->find($id);
        // dd($scheduleVisit);
        if (!$scheduleVisit) {
            Toastr::error('Schedule visit not found.', 'Error');
            return redirect()->route('field_manager.visiter.index');
        } else {
            return view('field_manager.visiter.show', compact('scheduleVisit'));
        }
    }


    public function sendConformationTemplateUser($propertyId, $user_id)
    {
        $scheduleVisit = ScheduleVisit::with('property')->where('property_id', $propertyId)->first();
        // dd($scheduleVisit);
        if (!$scheduleVisit) {
            Log::error('Schedule visit not found', ['property_id' => $propertyId]);
            Toastr::error('Schedule visit not found for the property.', 'Error');
            return;
        }

        $localityName = Locality::find($scheduleVisit->property->locality)->name ?? 'No locality found';
        $societyName = Society::find($scheduleVisit->property->society_name)->name ?? 'No society found';
        $cityName = Cities::find($scheduleVisit->property->city)->city_name ?? 'No city found';
        $imageUrl = asset('storage/property/' . $scheduleVisit->property->owner_id . '/' . $scheduleVisit->property->unique_id . '/' . $scheduleVisit->property->image);

        $scheduleVisitUserList = ScheduleVisitUserList::with('visit', 'user')->where('visite_id', $scheduleVisit->id)->where('user_id', $user_id)->get();
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

            $templateName = 'property_visit_confirmation_user';
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
            // $response = $this->whatsAppService->sendConformationForm(
            //     $phoneNumber,
            //     $templateName,
            //     $languageCode,
            //     $variables,
            //     $confirmationUrl,
            //     $imageUrl,
            //     $callingUrl
            // );
            $response = $this->InteraktWhatsAppService->sendPropertyVisitConfirmationUser(
                $phoneNumber,
                $userInfo->user->name,
                $scheduleVisit->property->title,
                $scheduleVisit->property->bhk,
                "$cityName, $localityName, $societyName",
                $scheduleVisit->property->unique_id,
                $confirmationUrl,
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
