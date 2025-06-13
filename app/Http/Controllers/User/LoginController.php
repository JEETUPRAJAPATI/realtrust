<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Owner;
use App\Models\User;
use App\Models\VerificationOtp;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    //
    use AuthenticatesUsers;
    public function login(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'mobile_no' => 'required|string',
            'otp' => 'required|string',
            'type' => 'required|in:user',
        ]);
        // Find the user/owner
        $user = User::class::where('mobile_no', $request->mobile_no)->first();
        // dd($user);
        if (!$user) {
            $user = User::create([
                'mobile_no' => $request->mobile_no
            ]);
        }
        // Verify OTP for the owner
        $verificationResponse = $this->verifyOtpUser($user->id, $request->otp, $request->type);
        if ($verificationResponse->getStatusCode() !== 200) {
            return $verificationResponse; // Return verification error response
        }
        $user->tokens()->delete();
        // Create a token for the owner
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'token' => $token,
            'is_user' => true,
            'user' => new UserResource($user) // Return the owner object
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
        if (Auth::guard('user')->check()) {
            $user = Auth::guard('sanctum')->user();
            $user->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully'], 200);
        }

        return response()->json(['message' => 'Not authenticated'], 401);
    }
}
