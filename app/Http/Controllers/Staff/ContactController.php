<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Http;
use App\Models\FieldManager;
use App\Models\User;
use App\Models\Staff;



class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::orderBy('id', 'desc')->get();
        return view('staff.contact.index', compact('contacts'));
    }
    public function destroy(string $id)
    {
        $contact = Contact::find($id);
        $contact->delete();
        Toastr::success('message', 'Contact deleted successfully.');
        return back();
    }
    public function show($id)
    {
        $contact = Contact::where('id', $id)->firstOrFail();
        return view('staff.contact.show', compact('contact'));
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:resolved,pending',
        ]);
        $contact = Contact::findOrFail($id);
        $contact->status = $request->status;
        $contact->save();

        return response()->json(['success' => true], 200);
    }
    
    public function makeCall (Request $request) {
        $apiUrl = "https://kpi.knowlarity.com/Basic/v1/account/call/makecall";
        
        $authKey = "68503d4e-a669-4a74-bb4c-b69d67087416";  // Authorization Key
        $apiKey = "QdQa83awS05tyB0KAVATX7tvm3WuBXz16QEluhix"; // API Key
        
    
        // API Request Payload
        $postData = [
            "k_number" => "+918929946479",
            "agent_number" =>  '+91'.$request->staff_number,
            "customer_number" => '+91'.$request->customer_number,
            "caller_id" => "+918035307559"
        ];
        
        // Make API Call
        $response = Http::withHeaders([
            "Authorization" => $authKey,  // Authorization Key
            "x-api-key" => $apiKey,      // API Key
            "Content-Type" => "application/json",
            "Accept" => "application/json"
        ])->post($apiUrl, $postData);

        // Handle Response
        if ($response->successful()) {
            return response()->json(["success" => true, "message" => "Call initiated successfully!", "data" => $response->json()]);
            Toastr::success('message', 'New Password cannot be same as your current password! Please choose a different password.');
        } else {
            return response()->json(["success" => false, "error" => $response->json()], $response->status());
            // Toastr::error('message', 'New Password cannot be same as your current password! Please choose a different password.');
        }

    }
    public function placeCall(Request $request,$uid,$fid) {
        
        // dd($request->all(),$uid,$fid);
        $field_manager = FieldManager::where('id',$fid)->first();
        $customer = User::where('id',$uid)->first();
        $apiUrl = "https://kpi.knowlarity.com/Basic/v1/account/call/makecall";
        
        $authKey = "68503d4e-a669-4a74-bb4c-b69d67087416";  // Authorization Key
        $apiKey = "QdQa83awS05tyB0KAVATX7tvm3WuBXz16QEluhix"; // API Key
        
        // API Request Payload
        $postData = [
            "k_number"=> "+918929946479",
            "agent_number" =>  "+91".$customer->mobile_no,
            "customer_number" => "+91".$field_manager->mobile_no,
            "caller_id" => "+918035307559"
        ];
        
        // Make API Call
        $response = Http::withHeaders([
            "Authorization" => $authKey,  // Authorization Key
            "x-api-key" => $apiKey,      // API Key
            "Content-Type" => "application/json",
            "Accept" => "application/json"
        ])->post($apiUrl, $postData);

        // Handle Response
        if ($response->successful()) {
            return response()->json(["success" => true, "message" => "Call initiated successfully!", "data" => $response->json()]);
        } else {
            return response()->json(["success" => false, "error" => $response->json()], $response->status());
        }
    }
    public function conferenceCall(Request $request,$uid,$sid) {
        
        $staff = Staff::where('id',$sid)->first();
        $customer = User::where('id',$uid)->first();
        $apiUrl = "https://kpi.knowlarity.com/Basic/v1/account/call/makecall";
        
        $authKey = "68503d4e-a669-4a74-bb4c-b69d67087416";  // Authorization Key
        $apiKey = "QdQa83awS05tyB0KAVATX7tvm3WuBXz16QEluhix"; // API Key
        
        // API Request Payload
        $postData = [
            "k_number"=> "+918929946479",
            "agent_number" =>  "+91".$customer->mobile_no,
            "customer_number" => "+91".$staff->mobile_no,
            "caller_id" => "+918035307559"
        ];
        
        // Make API Call
        $response = Http::withHeaders([
            "Authorization" => $authKey,  // Authorization Key
            "x-api-key" => $apiKey,      // API Key
            "Content-Type" => "application/json",
            "Accept" => "application/json"
        ])->post($apiUrl, $postData);

        // Handle Response
        if ($response->successful()) {
            return response()->json(["success" => true, "message" => "Call initiated successfully!", "data" => $response->json()]);
        } else {
            return response()->json(["success" => false, "error" => $response->json()], $response->status());
        }
    }
    
}
