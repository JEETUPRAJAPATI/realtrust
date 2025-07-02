<?php

namespace App\Services;

use App\Models\Property;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InteraktWhatsAppService
{
    protected $client;
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = 'https://api.interakt.ai/v1/public/message'; // Interakt message endpoint
        $this->apiKey = env('INTERAKT_API_KEY'); // Store your Interakt API Key in .env
        Log::info(' $this->baseUrl ', ['response baseUrl' => $this->baseUrl]);
        Log::info(' $this->apiKey ', ['response apiKey' => $this->apiKey]);
    }

    // cfdd7d88-cbb8-4101-bab0-e407c4637c47

    public function handle(Request $request)
    {
        $data = $request->all();
        $phone = $data['phoneNumber'] ?? null;
        $payload = $data['buttonPayload'] ?? null;
        $messageText = strtolower($data['messageText'] ?? '');

        if ($messageText === 'hi') {
            return $this->sendWelcomeMessage($phone);
        }

        if ($payload === 'rent') {
            return $this->sendLocalityOptions($phone);
        }

        if (str_starts_with($payload, 'locality:')) {
            $locality = explode(':', $payload)[1];
            return $this->sendTopPropertiesCarousel($phone, $locality);
        }

        return response()->json(['status' => 'ignored']);
    }
    private function sendWelcomeMessage($phone)
    {
        return $this->sendTemplate($phone, 'welcome_template', ['Friend'], [
            '0' => ['rent'],
            '1' => ['sell']
        ]);
    }



    private function sendLocalityOptions($phone)
    {
        return $this->sendTemplate($phone, 'locality_selector_template', [], [
            '0' => ['locality:banjara_hills'],
            '1' => ['locality:whitefield'],
            '2' => ['locality:sector_62']
        ]);
    }

    private function sendTopPropertiesCarousel($phone, $locality)
    {
        $properties = Property::where('locality', $locality)->take(5)->get();

        foreach ($properties as $property) {
            $this->sendTemplate(
                $phone,
                'property_carousel_template',
                [
                    $property->title,
                    $property->bhk,
                    $property->locality,
                    $property->available_for
                ],
                [
                    '0' => [$property->unique_id] // Schedule Visit URL param
                ]
            );
        }

        return response()->json(['message' => 'Properties sent.']);
    }

    private function sendTemplate($phone, $templateName, $body = [], $buttonValues = [])
    {
        $payload = [
            'fullPhoneNumber' => $phone,
            'callbackData' => $templateName,
            'type' => 'Template',
            'template' => [
                'name' => $templateName,
                'languageCode' => 'en',
                'bodyValues' => $body,
                'buttonValues' => (object)$buttonValues
            ]
        ];



        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . env('INTERAKT_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.interakt.ai/v1/public/message/', $payload);

            $result = json_decode($response->getBody(), true);
            Log::info("✅ Sent template [$templateName]", ['response' => $result]);

            return $result;
        } catch (\Exception $e) {
            Log::error("❌ Failed to send [$templateName]", [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Send 'schedule_visit_user' template message
     *
     * @param string $phone       Full phone number with country code (e.g., +919512087056)
     * @param string $userName    Template variable {{1}} - user's name
     * @param string $propertyName Template variable {{2}} - property name
     * @param string $location    Template variable {{3}} - property location
     * @param string $callbackData Optional custom callback data for tracking
     * @return array              API response
     */

    // $response = $interakt->sendScheduleVisitUser(
    //     '+919512087056',
    //     'Jeetu',
    //     'Dream Residency Flat',
    //     'Banjara Hills, Hyderabad'
    // );
    public function sendScheduleVisitUser(string $phone, string $userName, string $propertyName, string $location, string $callbackData = 'schedule_visit_user')
    {
        $payload = [
            'countryCode' => '+91',
            'phoneNumber' => $phone,
            'callbackData' => $callbackData,
            'type' => 'Template',
            'template' => [
                'name' => 'schedule_visit_user',
                'languageCode' => 'en',
                'bodyValues' => [
                    $userName,
                    $propertyName,
                    $location
                ]
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . env('INTERAKT_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.interakt.ai/v1/public/message/', $payload);

            $result = json_decode($response->getBody(), true);
            Log::info("Interakt: schedule_visit_user sent", ['response' => $result]);

            return $result;
        } catch (\Exception $e) {
            Log::error("Interakt: schedule_visit_user failed", [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send WhatsApp message.',
                'error' => $e->getMessage()
            ];
        }
    }


    /**
     * Send 'notify_pending_user_on_scheduled_visit' template message
     *
     * @param string $phone        Full phone number with country code
     * @param string $userName     Template {{1}} - User name
     * @param string $propertyName Template {{2}} - Property name
     * @param string $location     Template {{3}} - Property location
     * @param string $callbackData Optional callback identifier
     * @return array               API response
     */


    // $response = $interakt->sendNotifyPendingUserOnScheduledVisit(
    //     '+919512087056',
    //     'Jeetu',
    //     'Dream Residency Flat',
    //     'Banjara Hills, Hyderabad'
    // );

    // https://api.interakt.ai/v1/public/message/
    // POST
    // Payload
    // {
    //     "countryCode": "+91",
    //     "phoneNumber": "9142950245",
    //     "callbackData": "notify_pending_user_on_scheduled_visit",
    //     "type": "Template",
    //     "template": {
    //       "name": "notify_pending_user_on_scheduled_visit",
    //       "languageCode": "en",
    //       "bodyValues": [
    //         "Ritu Kumari",                  // {{1}} → user name
    //         "Dream Residency Flat",         // {{2}} → property name
    //         "Banjara Hills, Bangalore"      // {{3}} → location
    //       ]
    //     }
    //   }


    public function sendNotifyPendingUserOnScheduledVisit(string $phone, string $userName, string $propertyName, string $location, string $callbackData = 'notify_pending_user_on_scheduled_visit')
    {
        $payload = [
            'fullPhoneNumber' => $phone,
            'callbackData' => $callbackData,
            'type' => 'Template',
            'data' => [
                'templateName' => 'notify_pending_user_on_scheduled_visit',
                'languageCode' => 'en',
                'templateData' => [
                    'body' => [
                        ['text' => $userName],      // {{1}}
                        ['text' => $propertyName],  // {{2}}
                        ['text' => $location],      // {{3}}
                    ]
                ]
            ]
        ];

        try {
            $response = $this->client->post($this->baseUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload
            ]);

            $result = json_decode($response->getBody(), true);
            Log::info("Interakt: notify_pending_user_on_scheduled_visit sent", ['response' => $result]);

            return $result;
        } catch (\Exception $e) {
            Log::error("Interakt: notify_pending_user_on_scheduled_visit failed", [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send WhatsApp message.',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send 'owner_confirm_timing' template to property owner
     *
     * @param string $phone        Full phone number with country code
     * @param string $ownerName    Template {{1}} - Owner's name
     * @param string $propertyName Template {{2}} - Property name
     * @param string $address      Template {{3}} - Property address
     * @param string $bhkType      Template {{4}} - BHK type
     * @param string $propertyId   Template {{6}} - Used in button URL
     * @param string $callbackData Optional callback reference
     * @return array               API response
     */

    // $response = $interakt->sendOwnerConfirmTiming(
    //     '+919512087056',
    //     'Mr. Sharma',
    //     'Dream Residency Flat',
    //     'Banjara Hills, Hyderabad',
    //     '2 BHK',
    //     '12345' // propertyId used in the URL

    // );
    public function sendOwnerConfirmTiming(string $phone, string $ownerName, string $propertyName, string $address, string $bhkType, string $propertyId, string $callbackData = 'owner_confirm_timing')
    {
        $payload = [
            'fullPhoneNumber' => $phone,
            'callbackData' => $callbackData,
            'type' => 'Template',
            'template' => [
                'name' => 'owner_confirm_timing',
                'languageCode' => 'en',
                'bodyValues' => [
                    $ownerName,
                    $propertyName,
                    $address,
                    $bhkType
                ],
                'buttonValues' => (object)[
                    "0" => [$propertyId] // Must be an object, not array
                ]
            ]
        ];

        // Log the outgoing payload
        Log::info("sendOwnerConfirmTiming payload", ['response' => $payload]);

        try {

            Log::info('Final Payload Being Sent', $payload);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . env('INTERAKT_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.interakt.ai/v1/public/message/', $payload);

            Log::info('API Response Status', ['status' => $response->status()]);
            Log::info('API Response Body', ['body' => $response->body()]);


            Log::info("Interakt: owner_confirm_timing sent", ['response' => $response]);
            return $response->json();
        } catch (\Exception $e) {

            Log::error("Interakt: owner_confirm_timing failed", [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
            return [
                'success' => false,
                'message' => 'Failed to send WhatsApp message.',
                'error' => $e->getMessage()
            ];
        }
    }


    /**
     * Send 'field_manager_confirm_timing' template to Field Manager
     *
     * @param string $phone            Full phone number with country code
     * @param string $managerName      {{1}} - Field Manager Name
     * @param string $propertyName     {{2}} - Property Name
     * @param string $address          {{3}} - Property Address
     * @param string $bhkType          {{4}} - BHK Type
     * @param string $callbackData     Optional callback reference
     * @return array                   API response
     */

    //       response = $interakt->sendFieldManagerConfirmTiming(
    //      '+919512087056',
    //      'Ravi Singh',
    //      'Palm Grove Apartments',
    //      'Sector 62, Noida',
    //      '3 BHK'
    // );
    public function sendFieldManagerConfirmTiming(string $phone, string $managerName, string $propertyName, string $address, string $bhkType, string $propertyId, string $callbackData = 'field_manager_confirm_timing')
    {
        $payload = [
            'fullPhoneNumber' => $phone,
            'callbackData' => $callbackData,
            'type' => 'Template',
            'template' => [
                'name' => 'field_manager_confirm_timing',
                'languageCode' => 'en',
                'bodyValues' => [
                    $managerName,
                    $propertyName,
                    $address,
                    $bhkType
                ],
                'buttonValues' => (object)[
                    "0" => [$propertyId] // Must be an object, not array
                ]
            ]
        ];

        // Log the outgoing payload
        Log::info("sendOwnerConfirmTiming payload", ['response' => $payload]);

        try {

            Log::info('Final Payload Being Sent', $payload);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . env('INTERAKT_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.interakt.ai/v1/public/message/', $payload);

            Log::info('API Response Status', ['status' => $response->status()]);
            Log::info('API Response Body', ['body' => $response->body()]);


            Log::info("Interakt: owner_confirm_timing sent", ['response' => $response]);
            return $response->json();
        } catch (\Exception $e) {

            Log::error("Interakt: field_manager_confirm_timing failed", [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send WhatsApp message.',
                'error' => $e->getMessage()
            ];
        }
    }


    /**
     * Send 'visit_confirm_user' message after successful visit scheduling
     *
     * @param string $phone            User phone number (with country code)
     * @param string $userName         {{1}} - User name
     * @param string $property         {{2}} - Property name
     * @param string $bhkType          {{3}} - BHK type
     * @param string $scheduledTime    {{4}} - Scheduled time
     * @param string $address          {{5}} - Property address
     * @param string $managerContact   {{6}} - Field manager masked contact
     * @param string $staffId          {{7}} - Staff ID (for call link)
     * @param string $sessionId        {{8}} - Session ID or token
     * @param string $callbackData     Optional reference
     * @return array
     */
    // $response = app(\App\Services\InteraktWhatsAppService::class)->sendVisitConfirmUser(
    //     '+919512087056',
    //     'Jeetu Prajapati',
    //     'Palm Grove Apartments',
    //     '3 BHK',
    //     '14 June, 4:00 PM',
    //     'Sector 62, Noida',
    //     '91XXXXXX21',
    //     'staff123',
    //     'sess456'
    // );

    public function sendVisitConfirmUser(string $phone, string $userName, string $property, string $bhkType, string $scheduledTime, string $address, string $managerContact, string $confirmationUrl, string $callingUrl, string $callbackData = 'visit_confirm_user')
    {
        $payload = [
            'fullPhoneNumber' => $phone,
            'callbackData' => $callbackData,
            'type' => 'Template',
            'template' => [
                'name' => 'visit_confirm_user',
                'languageCode' => 'en',
                'bodyValues' => [
                    $userName,
                    $property,
                    $bhkType,
                    $scheduledTime,
                    $address,
                    $managerContact
                ],
                'buttonValues' =>  (object)[
                    '0' => [$confirmationUrl],
                    '1' => [$callingUrl]
                ]
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . env('INTERAKT_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.interakt.ai/v1/public/message/', $payload);

            $result = json_decode($response->getBody(), true);
            Log::info('✅ Interakt: visit_confirm_user sent', ['response' => $result]);
            return $result;
        } catch (\Exception $e) {
            Log::error('❌ Interakt: visit_confirm_user failed', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
            return [
                'success' => false,
                'message' => 'Failed to send WhatsApp visit confirmation message.',
                'error' => $e->getMessage()
            ];
        }
    }



    /**
     * Send 'notify_field_manager_to_visit' to Field Manager
     *
     * @param string $phone            Field manager phone number (with country code)
     * @param string $name             {{1}} - Manager name
     * @param string $propertyTitle    {{2}} - Property title
     * @param string $visitTime        {{3}} - Visit time
     * @param string $address          {{4}} - Property address
     * @param string $gatePass         {{5}} - Gate pass
     * @param string $flatBlock        {{6}} - Flat/Block
     * @param string $staffId          {{7}} - Staff ID for call URL
     * @param string $sessionId        {{8}} - Session ID for call URL
     * @param string $callbackData     Optional callback identifier
     * @return array
     */

    //  $response = app(\App\Services\InteraktWhatsAppService::class)->sendNotifyFieldManagerToVisit(
    //     '+919512087056',
    //     'Amit Manager',
    //     'Palm Grove Apartments',
    //     '14 June, 4:00 PM',
    //     'Sector 62, Noida',
    //     'Gate Pass #42',
    //     'Flat 2B, Block C',
    //     'staff123',
    //     'sess456'
    // );

    public function sendNotifyFieldManagerToVisit(
        string $phone,
        string $name,
        string $propertyTitle,
        string $visitTime,
        string $address,
        string $gatePass,
        string $flatBlock,
        string $callingUrl,
        string $callbackData = 'notify_field_manager_to_visit'
    ) {

        $payload = [
            'fullPhoneNumber' => $phone,
            'callbackData' => $callbackData,
            'type' => 'Template',
            'template' => [
                'name' => 'notify_field_manager_to_visit',
                'languageCode' => 'en',
                'bodyValues' => [
                    $name,
                    $propertyTitle,
                    $visitTime,
                    $address,
                    $gatePass,
                    $flatBlock
                ],
                'buttonValues' =>  (object)[
                    '0' => [$callingUrl] // maps to {{7}} and {{8}} in the button URL
                ]
            ]
        ];
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . env('INTERAKT_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.interakt.ai/v1/public/message/', $payload);


            $result = json_decode($response->getBody(), true);
            Log::info('Interakt: notify_field_manager_to_visit sent', ['response' => $result]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Interakt: notify_field_manager_to_visit failed', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return [
                'success' => false,
                'message' => 'Failed to notify field manager.',
                'error' => $e->getMessage()
            ];
        }
    }


    /**
     * Send 'Join_pending_user_schedule_visit' template to user
     *
     * @param string $phone         Recipient phone number (with country code)
     * @param string $userName      {{1}} - User name
     * @param string $propertyName  {{2}} - Property name
     * @param string $dateTime      {{3}} - Visit date & time
     * @param string $location      {{4}} - Property location
     * @param string $fieldContact  {{5}} - Field manager contact
     * @param string $staffId       {{6}} - Staff ID for call URL
     * @param string $sessionId     {{7}} - Session ID for call URL
     * @param string $callbackData  Optional callback identifier
     * @return array
     */

    //  $response = app(\App\Services\InteraktWhatsAppService::class)->sendJoinPendingUserScheduleVisit(
    //     '+919512087056',
    //     'Jeetu',
    //     'Green Residency',
    //     '15 June, 3:00 PM',
    //     'Sector 137, Noida',
    //     'field987',
    //     'staff123',
    //     'sess456'
    // );

    public function sendJoinPendingUserScheduleVisit(
        string $phone,
        string $userName,
        string $propertyName,
        string $dateTime,
        string $location,
        string $fieldContact,
        string $confirmationUrl,
        string $callingUrl,
        string $callbackData = 'join_pending_user_schedule_visit'
    ) {
        $payload = [
            'fullPhoneNumber' => $phone,
            'callbackData' => $callbackData,
            'type' => 'Template',
            'template' => [
                'name' => 'join_pending_user_schedule_visit',
                'languageCode' => 'en',
                'bodyValues' => [
                    $userName,
                    $propertyName,
                    $dateTime,
                    $location,
                    $fieldContact
                ],
                'buttonValues' => (object)[
                    '0' => [$confirmationUrl],
                    '1' => [$callingUrl]
                ]
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . env('INTERAKT_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.interakt.ai/v1/public/message/', $payload);


            $result = json_decode($response->getBody(), true);
            Log::info('Interakt: Join_pending_user_schedule_visit sent', ['response' => $result]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Interakt: Join_pending_user_schedule_visit failed', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send join visit confirmation.',
                'error' => $e->getMessage()
            ];
        }
    }


    /**
     * Send 'property_visit_confirmation_user' template after visit completion
     *
     * @param string $phone         Recipient phone number (with country code)
     * @param string $userName      {{1}} - User Name
     * @param string $propertyTitle {{2}} - Property Title
     * @param string $bhkType       {{3}} - BHK Type
     * @param string $address       {{4}} - Address
     * @param string $propertyId    {{5}} - Property ID for Book Now URL
     * @param string $staffId       {{6}} - Staff ID for Connect Now
     * @param string $sessionId     {{7}} - Session ID for Connect Now
     * @param string $callbackData  Optional callback identifier
     * @return array
     */

    //  $response = app(\App\Services\InteraktWhatsAppService::class)->sendPropertyVisitConfirmationUser(
    //     '+919512087056',
    //     'Jeetu',
    //     'Sunshine Villa',
    //     '2BHK',
    //     'MG Road, Bangalore',
    //     '123456',
    //     'staff456',
    //     'sess789'
    // );

    public function sendPropertyVisitConfirmationUser(
        string $phone,
        string $userName,
        string $propertyTitle,
        string $bhkType,
        string $address,
        string $confirmationUrl,
        string $callingUrl,
        string $callbackData = 'property_visit_confirmation_user'
    ) {
        $payload = [
            'fullPhoneNumber' => $phone,
            'callbackData' => $callbackData,
            'type' => 'Template',
            'template' => [
                'name' => 'property_visit_confirmation_user_5g',
                'languageCode' => 'en',
                'bodyValues' => [
                    $userName,         // {{1}}
                    $propertyTitle,    // {{2}}
                    $bhkType,          // {{3}}
                    $address           // {{4}}
                ],
                'buttonValues' =>  (object)[
                    '0' => [$confirmationUrl], // {{5}} → e.g., property-123#payments
                    '1' => [$callingUrl]          // {{6}} → e.g., staff456
                ]
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . env('INTERAKT_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.interakt.ai/v1/public/message/', $payload);

            $result = json_decode($response->getBody(), true);
            Log::info('✅ Interakt: property_visit_confirmation_user sent', ['response' => $result]);

            return $result;
        } catch (\Exception $e) {
            Log::error('❌ Interakt: property_visit_confirmation_user failed', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send visit confirmation follow-up.',
                'error' => $e->getMessage()
            ];
        }
    }



    /**
     * Send 'otp_verification_message' for OTP-based login/verification
     *
     * @param string $phone       Recipient phone number (with country code)
     * @param string $otp         {{1}} - One-Time Password
     * @param string $callbackData Optional callback identifier
     * @return array
     */

    //  $response = app(\App\Services\InteraktWhatsAppService::class)->sendOtpVerificationMessage(
    //     '+919512087056',
    //     '829104'
    // );
    public function sendOtpVerificationMessage(string $phone, string $otp)
    {
        $payload = [
            'fullPhoneNumber' => $phone,
            'callbackData' => 'otp_verification_message',
            'type' => 'Template',
            'template' => [
                'name' => 'otp_verification_message',
                'languageCode' => 'en',
                'bodyValues' => [$otp],
                'buttonValues' => (object)[
                    '0' => [$otp]
                ]
            ],
        ];
        Log::info('Final Payload Being Sent', $payload);
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . env('INTERAKT_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.interakt.ai/v1/public/message/', $payload);

        Log::info('API Response Status', ['status' => $response->status()]);
        Log::info('API Response Body', ['body' => $response->body()]);

        return $response->json();
    }
}
