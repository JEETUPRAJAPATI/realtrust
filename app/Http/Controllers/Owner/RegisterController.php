<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:owners',
            'mobile_no' => 'required|string|digits_between:10,15|unique:owners,mobile_no',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),  // Return validation errors
                'message' => 'Validation failed',
            ], 422);
        }

        $owner = Owner::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile_no' => $request->mobile_no,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status' => true,
            'owner' => $owner,
            'message' => 'Owner registered successfully',
        ], 201);
    }
}
