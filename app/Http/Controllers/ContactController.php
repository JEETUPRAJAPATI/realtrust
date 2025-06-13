<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Inquery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email',
            'phone' => 'required|string|max:15',
            'message' => 'required|string|max:500',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create a new contact
        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contact created successfully!',
            'data' => $contact,
        ], 201);
    }

    public function inquery(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:inqueries,email',
            'phone' => 'required|string|max:15',
            'society_name' => 'required|string|max:500',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create a new contact
        $contact = Inquery::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'society_name' => $request->society_name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inquery add successfully!',
            'data' => $contact,
        ], 201);
    }
    
     public function handle(Request $request)
    {
        // ✅ STEP 1: Handle webhook verification from Meta
        if ($request->isMethod('get')) {
            $verifyToken = env('WHATSAPP_TOKEN', 'default_token');
    
            if (
                $request->get('hub_mode') === 'subscribe' &&
                $request->get('hub_verify_token') === $verifyToken
            ) {
                return response($request->get('hub_challenge'), 200);
            }
    
            return response('Invalid verification token', 403);
        }
    
        // ✅ STEP 2: Log incoming webhook events
       Log::info('WhatsApp Webhook Received: ' . json_encode($request->all(), JSON_PRETTY_PRINT));
    
        return response()->json(['status' => 'received'], 200);
    }
}
