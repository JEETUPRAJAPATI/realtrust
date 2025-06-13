<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Paymentes;
use App\Models\Property;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
        private $merchantId = "UATMERCHANT"; // Use provided Merchant ID
        private $saltKey = "8289e078-be0b-484d-ae60-052f117f8deb"; // Use Testing Key
        private $saltIndex = 1; // Use Key Index
    
        private $baseUrl = "https://api-preprod.phonepe.com/apis/pg-sandbox"; // UAT base URL

    /**
     * Show the payment form to the user
     */
    public function showPaymentForm()
    {
        return view('payment');
    }

    /**
     * Create an order on Razorpay and return the order ID
     */
    
    public function createOrder(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }
        // Fetch the property from the database
        $property = Property::where('unique_id', $request->input('property_id'))->first();
        $payments_exist = Paymentes::where('property_id', $request->input('property_id'))
            ->where('payment_type', 'token')
            ->first();
        
        $paymentType = $payments_exist ? 'final' : 'token';
        
        // Check if property exists
        if (!$property) {
            return response()->json(['error' => 'Property not found'], 404);
        }
        $total = $request->input('amount');

        // Fetch Razorpay credentials from .env
        $key = env('RAZORPAY_KEY');
        $secret = env('RAZORPAY_SECRET');

        if (empty($key) || empty($secret)) {
            return response()->json(['error' => 'Razorpay keys are missing or incorrect']);
        }
        // Create an order
        $orderData = [
            'receipt'         => time(),
            'amount'          => $request->input('amount') * 100, // Amount in paise
            'currency'        => 'INR',
            'payment_capture' => 1,
        ];
        // Initialize CURL for Razorpay API
        $ch = curl_init();

        // Set the CURL options for the Razorpay order creation request
        curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$key:$secret");  // Basic Authentication
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($orderData)); // Send data as URL-encoded form data
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        // Execute the request and fetch the response
        $response = curl_exec($ch);
        // Log the raw response to debug the issue
        Log::info('Razorpay API Response:', ['response' => $response]);

        // Check if the CURL request failed
        if (curl_errno($ch)) {
            Log::error('CURL Error: ' . curl_error($ch));
            curl_close($ch);
            return response()->json(['error' => 'Unable to create order.']);
        }

        // Close the CURL connection
        curl_close($ch);

        // Decode the response
        $responseData = json_decode($response, true);

        // Log the decoded response data
        Log::info('Decoded Razorpay API Response:', ['responseData' => $responseData]);
        // Check for Razorpay order creation success
        if (isset($responseData['id'])) {
            // Save order to the database
            $user = Auth::guard('user')->user();
            $payment = Paymentes::create([
                'property_id' => $request->property_id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile_no,
                'payment_type' => $paymentType,
                'currency' => 'INR',
                'amount' => $total,
                'method' => 'razorpay',
                'order_id' => $responseData['id'],
                'status' => 'pending', // Order status
            ]);

            // Return the order details for frontend integration
            return response()->json([
                'order_id' => $responseData['id'],
                'amount' => $total,
                'key' => $key
            ]);
        } else {
            // Handle failure response
            return response()->json(['error' => 'Unable to create Razorpay order.', 'message' => $response]);
        }
    }
    public function createOrder1(Request $request)
    {
        $merchantId = env('PHONEPE_MERCHANT_ID');
        $saltKey = env('PHONEPE_SALT_KEY');
        $saltIndex = env('PHONEPE_SALT_INDEX');
        $callbackUrl = route('phonepe.callback');
    
        // Payment Data
        $payload = [
            "merchantId" => $merchantId,
            "merchantTransactionId" => uniqid(),
            "merchantUserId" => "USER123",
            "amount" => 10000, // Amount in paise (₹100)
            "redirectUrl" => $callbackUrl,
            "callbackUrl" => $callbackUrl,
            "mobileNumber" => "9999999999",
            "paymentInstrument" => [
                "type" => "PAY_PAGE"
            ]
        ];

        $jsonPayload = json_encode($payload);
        $base64Payload = base64_encode($jsonPayload);
    
        // Checksum as per PhonePe spec
        $checksum = hash('sha256', $base64Payload . "/pg/v1/pay" . $saltKey) . "###" . $saltIndex;
    
        // Send request to sandbox
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-VERIFY' => $checksum
        ])->post("https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay", [
            'request' => $base64Payload
        ]);
    
        return $response->json(); // Dump response


        $responseData = $response->json();
        
        if ($responseData['success']) {
            return redirect($responseData['data']['instrumentResponse']['redirectInfo']['url']);
        } else {
            return response()->json($responseData);
        }   
    }

    /**
     * Handle the payment callback from Razorpay after payment
     */
    public function paymentCallback(Request $request)
    {
     dd('jjj');   
        return response()->json($request->all());
    }

    public function paymentCallback1(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|string',
            'payment_id' => 'required|string',
            'signature' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $orderId = $request->order_id;
        $paymentId = $request->payment_id;
        $signature = $request->signature;

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        try {
            $attributes = [
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
            ];

            // Verify the payment signature
            $api->utility->verifyPaymentSignature($attributes);

            // Payment verification passed
            $payment = Paymentes::where('order_id', $orderId)->first();
            if ($payment) {
                $payment->payment_id = $paymentId;
                $payment->signature = $signature;
                $payment->status = 'success';
                $payment->json_response = json_encode($request->all());
                $payment->save();
                // Fetch the associated property using payment's property_id
                $property = Property::find($payment->property_id);
                if ($property) {
                    $property->status = 'Sold';
                    $property->save();
                    return response()->json(['message' => 'Payment successfully verified and property status updated']);
                } else {
                    return response()->json(['error' => 'Property not found for this payment']);
                }
            } else {
                return response()->json(['error' => 'Order not found']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment verification failed']);
        }
    }
    

    // public function store(Request $request)
    // {
    //     // Razorpay API Key and Secret from .env
    //     $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

    //     // Generate order
    //     $orderData = [
    //         'receipt' => 'order_rcptid_11',
    //         'amount' => $request->amount * 100, // amount in paise
    //         'currency' => 'INR',
    //         'payment_capture' => 1, // auto capture payment
    //     ];

    //     $order = $api->order->create($orderData);
    //     // dd($order);
    //     return response()->json(['order' => $order]);
    // }
    // public function verifyPayment(Request $request)
    // {
    //     $request->validate([
    //         'razorpay_order_id' => 'required|string',
    //         'razorpay_payment_id' => 'required|string',
    //         'razorpay_signature' => 'required|string',
    //     ]);

    //     $api_key = env('RAZORPAY_KEY');
    //     $api_secret = env('RAZORPAY_SECRET');

    //     // Create a signature string
    //     $signature = hash_hmac('sha256', $request->razorpay_order_id . '|' . $request->razorpay_payment_id, $api_secret);

    //     if ($signature === $request->razorpay_signature) {
    //         // Payment is successful, update payment status
    //         $payment = Paymentes::where('payment_id', $request->razorpay_order_id)->first();
    //         if ($payment) {
    //             $payment->status = 'completed';
    //             $payment->save();
    //             return response()->json(['message' => 'Payment successful!'], 200);
    //         }
    //     }

    //     return response()->json(['message' => 'Payment verification failed!'], 400);
    // }

    public function paymentHistory(Request $request)
    {
        $userEmail = Auth::guard('user')->user()->email;
        $perPage = $request->input('per_page', 10);
        $paymentHistory = Paymentes::with('property:unique_id,title')->where('email', $userEmail)->paginate($perPage);

        if ($paymentHistory->isNotEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'Payment history retrieved successfully.',
                'data' => $paymentHistory
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No payment history found for this user.',
                'data' => null
            ], 404);
        }
    }

    // public function paymentCallback(Request $request)
    // {
    //     $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

    //     $attributes = [
    //         'razorpay_order_id' => $request->razorpay_order_id,
    //         'razorpay_payment_id' => $request->razorpay_payment_id,
    //         'razorpay_signature' => $request->razorpay_signature
    //     ];

    //     try {
    //         $api->utility->verifyPaymentSignature($attributes);

    //         // Save payment details to the database
    //         $payment = new Paymentes();
    //         $payment->property_id = $request->property_id;  // Assuming you have the property_id from the form
    //         $payment->name = $request->name;
    //         $payment->email = $request->email;
    //         $payment->mobile = $request->mobile;
    //         $payment->currency = 'INR';  // Static or dynamic depending on your implementation
    //         $payment->amount = $request->amount;
    //         $payment->method = 'Razorpay';  // Payment method
    //         $payment->payment_id = $request->razorpay_payment_id;
    //         $payment->json_response = json_encode($request->all()); // Store the full Razorpay response
    //         $payment->status = 'completed'; // Change this based on payment verification
    //         $payment->save();

    //         return response()->json(['status' => 'Payment Successful']);
    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 'Payment Failed', 'message' => $e->getMessage()]);
    //     }
    // }
    
}
