<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VerificationOtp;
use App\Services\WhatsAppService;

use App\Services\InteraktWhatsAppService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller
{


    protected $InteraktWhatsAppService;

    public function __construct(InteraktWhatsAppService $InteraktWhatsAppService)
    {
        $this->InteraktWhatsAppService = $InteraktWhatsAppService;
    }

    public function requestOtp(Request $request)
    {
        $request->validate([
            'mobile_no' => 'required|numeric|digits:10',
        ]);

        if ($request->type === 'owner') {
            $owner = Owner::where('mobile_no', $request->mobile_no)->first();

            if (!$owner) {
                // return response()->json(['message' => ucfirst($request->type) . ' not found'], 404);
                $owner = Owner::create([
                    'mobile_no' => $request->mobile_no
                ]);
            }

            $otp = $this->generateOtp($owner->id, $request->type);
            $response = $this->sendOtp($owner->mobile_no, $otp);

            // Check the response from the sendOtp function
            if (isset($response['error']) && $response['error'] === true) {
                return response()->json([
                    'message' => 'Failed to send OTP to owner: ' . $response['message'],
                    'error_code' => $response['error_code'] ?? 'unknown',
                ], 500);
            }

            return response()->json([
                'message' => 'OTP sent successfully to owner',
                // 'otp' => $otp
            ]);
        }

        if ($request->type === 'user') {
            $user = User::where('mobile_no', $request->mobile_no)->first();
            if (!$user) {
                // return response()->json(['message' => ucfirst($request->type) . ' not found'], 404);
                $user = User::create([
                    'mobile_no' => $request->mobile_no
                ]);
            }
            $otp = $this->generateOtp($user->id, $request->type);
            $response = $this->sendOtp($user->mobile_no, $otp);
            // Check the response from the sendOtp function
            if (isset($response['error']) && $response['error'] === true) {
                return response()->json([
                    'message' => 'Failed to send OTP to user: ' . $response['message'],
                    'error_code' => $response['error_code'] ?? 'unknown',
                ], 500);
            }
            // 'otp' => $otp
            return response()->json([
                'message' => 'OTP sent successfully to user',
                // 'otp' => $otp
            ]);
        }

        // return response()->json(['message' => 'Mobile number not found'], 404);
    }

    function sendOtp($phone, $otp)
    {
        // dd('dsa');
        if (!str_starts_with($phone, '+91')) {
            $phone = '+91' . ltrim($phone, '0');
        }
        // Log::info('phone', ['phone' =>  $phone]);
        // die();
        $response = $this->InteraktWhatsAppService->sendOtpVerificationMessage($phone, $otp);
        //  $response = app(\App\Services\InteraktWhatsAppService::class)->sendOtpVerificationMessage(
        //     '+919512087056',
        //     '829104'
        // );
        if (isset($response['error']) && $response['error'] === true) {
            WhatsappMessage::create([
                'phone_number' => $phone,
                'template_name' => 'otp_verification_message_o8',
                'variables' => json_encode(value: $otp),
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
                'template_name' => 'otp_verification_message',
                'variables' => json_encode($otp),
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
}
