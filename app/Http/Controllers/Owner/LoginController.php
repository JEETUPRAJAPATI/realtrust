<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\OwnerResource;
use App\Models\Owner;
use App\Models\VerificationOtp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'mobile_no' => 'required|string',
            'otp' => 'required|string',
            'type' => 'required|in:owner',
        ]);
        // Find the user/owner
        $owner = Owner::class::where('mobile_no', $request->mobile_no)->first();
        // dd($owner);
        if (!$owner) {
            // return response()->json(['message' => ucfirst($request->type) . ' not found'], 404);
            $owner = Owner::create([
                'mobile_no' => $request->mobile_no
            ]);
        }
        // Verify OTP for the owner
        $verificationResponse = $this->verifyOtpUser($owner->id, $request->otp, $request->type);
        if ($verificationResponse->getStatusCode() !== 200) {
            return $verificationResponse; // Return verification error response
        }
        // Create a token for the owner
        $token = $owner->createToken('authToken')->plainTextToken;
        return response()->json([
            'token' => $token,
            'is_owner' => true,
            'user' => new OwnerResource($owner) // Return the owner object
        ], 200);
    }

    function verifyOtpUser($entityId, $inputOtp, $type)
    {
        $otpRecord = VerificationOtp::where($type . '_id', $entityId)
            ->where('otp', $inputOtp)
            ->where('is_used', false)
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid OTP'], 401);
        }

        if (Carbon::now()->greaterThan($otpRecord->expires_at)) {
            return response()->json(['message' => 'OTP expired'], 401);
        }

        $otpRecord->update(['is_used' => true]);

        return response()->json(['message' => 'OTP verified successfully'], 200);
    }

    public function logout(Request $request)
    {
        if (Auth::guard('owner')->check()) {
            $owner = Auth::guard('owner')->user();
            $owner->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully'], 200);
        }

        return response()->json(['message' => 'Not authenticated'], 401);
    }
}
