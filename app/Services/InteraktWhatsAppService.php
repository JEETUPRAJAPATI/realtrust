<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class InteraktWhatsAppService
{
    protected $client;
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = 'https://api.interakt.ai/v1/public/message/'; // Interakt message endpoint
        $this->apiKey = env('INTERAKT_API_KEY'); // Store your Interakt API Key in .env
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
            'fullPhoneNumber' => $phone,
            'callbackData' => $callbackData,
            'type' => 'Template',
            'data' => [
                'templateName' => 'schedule_visit_user',
                'languageCode' => 'en',
                'templateData' => [
                    'body' => [
                        ['text' => $userName],       // {{1}}
                        ['text' => $propertyName],   // {{2}}
                        ['text' => $location],       // {{3}}
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
            Log::info("✅ Interakt: schedule_visit_user sent", ['response' => $result]);

            return $result;
        } catch (\Exception $e) {
            Log::error("❌ Interakt: schedule_visit_user failed", [
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
            Log::info("✅ Interakt: notify_pending_user_on_scheduled_visit sent", ['response' => $result]);

            return $result;
        } catch (\Exception $e) {
            Log::error("❌ Interakt: notify_pending_user_on_scheduled_visit failed", [
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
            'data' => [
                'templateName' => 'owner_confirm_timing',
                'languageCode' => 'en',
                'templateData' => [
                    'body' => [
                        ['text' => $ownerName],     // {{1}}
                        ['text' => $propertyName],  // {{2}}
                        ['text' => $address],       // {{3}}
                        ['text' => $bhkType],       // {{4}}
                    ],
                    'buttons' => [
                        [
                            'type' => 'url',
                            'text' => 'Confirm Timing',
                            'url' => 'https://admin.realtrust.in/conform-timing/' . $propertyId // {{6}}
                        ]
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
            Log::info("✅ Interakt: owner_confirm_timing sent", ['response' => $result]);

            return $result;
        } catch (\Exception $e) {
            Log::error("❌ Interakt: owner_confirm_timing failed", [
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

    //  $response = $interakt->sendFieldManagerConfirmTiming(
    //     '+919512087056',
    //     'Ravi Singh',
    //     'Palm Grove Apartments',
    //     'Sector 62, Noida',
    //     '3 BHK'
    // );
    public function sendFieldManagerConfirmTiming(string $phone, string $managerName, string $propertyName, string $address, string $bhkType, string $callbackData = 'field_manager_confirm_timing')
    {
        $payload = [
            'fullPhoneNumber' => $phone,
            'callbackData' => $callbackData,
            'type' => 'Template',
            'data' => [
                'templateName' => 'field_manager_confirm_timing',
                'languageCode' => 'en',
                'templateData' => [
                    'body' => [
                        ['text' => $managerName],     // {{1}}
                        ['text' => $propertyName],    // {{2}}
                        ['text' => $address],         // {{3}}
                        ['text' => $bhkType],         // {{4}}
                    ],
                    'buttons' => [
                        [
                            'type' => 'url',
                            'text' => 'Confirm Viewing',
                            'url' => 'https://admin.realtrust.in/conformtiming/field_manager/' . urlencode($managerName) // dynamic {{1}} as part of URL
                        ]
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
            Log::info("✅ Interakt: field_manager_confirm_timing sent", ['response' => $result]);

            return $result;
        } catch (\Exception $e) {
            Log::error("❌ Interakt: field_manager_confirm_timing failed", [
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

    public function sendVisitConfirmUser(
        string $phone,
        string $userName,
        string $property,
        string $bhkType,
        string $scheduledTime,
        string $address,
        string $managerContact,
        string $staffId,
        string $sessionId,
        string $callbackData = 'visit_confirm_user'
    ) {
        $payload = [
            'fullPhoneNumber' => $phone,
            'callbackData' => $callbackData,
            'type' => 'Template',
            'data' => [
                'templateName' => 'visit_confirm_user',
                'languageCode' => 'en',
                'templateData' => [
                    'body' => [
                        ['text' => $userName],         // {{1}}
                        ['text' => $property],         // {{2}}
                        ['text' => $bhkType],          // {{3}}
                        ['text' => $scheduledTime],    // {{4}}
                        ['text' => $address],          // {{5}}
                        ['text' => $managerContact],   // {{6}}
                    ],
                    'buttons' => [
                        [
                            'type' => 'url',
                            'text' => 'Track Field Manager',
                            'url' => 'https://admin.realtrust.in/field-manager/' . urlencode($managerContact)
                        ],
                        [
                            'type' => 'url',
                            'text' => 'Call To Staff',
                            'url' => 'https://admin.realtrust.in/staff/place-call/' . urlencode($staffId) . '/' . urlencode($sessionId)
                        ]
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
        string $staffId,
        string $sessionId,
        string $callbackData = 'notify_field_manager_to_visit'
    ) {
        $payload = [
            'fullPhoneNumber' => $phone,
            'callbackData' => $callbackData,
            'type' => 'Template',
            'data' => [
                'templateName' => 'notify_field_manager_to_visit',
                'languageCode' => 'en',
                'templateData' => [
                    'body' => [
                        ['text' => $name],           // {{1}}
                        ['text' => $propertyTitle],  // {{2}}
                        ['text' => $visitTime],      // {{3}}
                        ['text' => $address],        // {{4}}
                        ['text' => $gatePass],       // {{5}}
                        ['text' => $flatBlock],      // {{6}}
                    ],
                    'buttons' => [
                        [
                            'type' => 'url',
                            'text' => 'Call To Staff',
                            'url' => 'https://admin.realtrust.in/staff/place-call/' . urlencode($staffId) . '/' . urlencode($sessionId)
                        ]
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
            Log::info('✅ Interakt: notify_field_manager_to_visit sent', ['response' => $result]);

            return $result;
        } catch (\Exception $e) {
            Log::error('❌ Interakt: notify_field_manager_to_visit failed', [
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
        string $staffId,
        string $sessionId,
        string $callbackData = 'Join_pending_user_schedule_visit'
    ) {
        $payload = [
            'fullPhoneNumber' => $phone,
            'callbackData' => $callbackData,
            'type' => 'Template',
            'data' => [
                'templateName' => 'Join_pending_user_schedule_visit',
                'languageCode' => 'en',
                'templateData' => [
                    'body' => [
                        ['text' => $userName],       // {{1}}
                        ['text' => $propertyName],   // {{2}}
                        ['text' => $dateTime],       // {{3}}
                        ['text' => $location],       // {{4}}
                        ['text' => $fieldContact],   // {{5}}
                    ],
                    'buttons' => [
                        [
                            'type' => 'url',
                            'text' => 'View Field Manager',
                            'url' => 'https://admin.realtrust.in/field-manager/' . urlencode($fieldContact)
                        ],
                        [
                            'type' => 'url',
                            'text' => 'Call Support',
                            'url' => 'https://admin.realtrust.in/staff/place-call/' . urlencode($staffId) . '/' . urlencode($sessionId)
                        ]
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
            Log::info('✅ Interakt: Join_pending_user_schedule_visit sent', ['response' => $result]);

            return $result;
        } catch (\Exception $e) {
            Log::error('❌ Interakt: Join_pending_user_schedule_visit failed', [
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
        string $propertyId,
        string $staffId,
        string $sessionId,
        string $callbackData = 'property_visit_confirmation_user'
    ) {
        $payload = [
            'fullPhoneNumber' => $phone,
            'callbackData' => $callbackData,
            'type' => 'Template',
            'data' => [
                'templateName' => 'property_visit_confirmation_user',
                'languageCode' => 'en',
                'templateData' => [
                    'body' => [
                        ['text' => $userName],        // {{1}}
                        ['text' => $propertyTitle],   // {{2}}
                        ['text' => $bhkType],         // {{3}}
                        ['text' => $address],         // {{4}}
                    ],
                    'buttons' => [
                        [
                            'type' => 'url',
                            'text' => 'Book Now',
                            'url' => 'https://realtrust.in/property/' . urlencode($propertyId) . '#payments'
                        ],
                        [
                            'type' => 'url',
                            'text' => 'Connect Now',
                            'url' => 'https://admin.realtrust.in/staff/conference-call/' . urlencode($staffId) . '/' . urlencode($sessionId)
                        ]
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

    public function sendOtpVerificationMessage(string $phone, string $otp, string $callbackData = 'otp_verification_message')
    {
        $payload = [
            'fullPhoneNumber' => $phone,
            'callbackData' => $callbackData,
            'type' => 'Template',
            'data' => [
                'templateName' => 'otp_verification_message',
                'languageCode' => 'en',
                'templateData' => [
                    'body' => [
                        ['text' => $otp], // {{1}} = OTP
                    ],
                    'buttons' => [
                        [
                            'type' => 'copy_code',
                            'text' => 'Copy OTP',
                            'value' => $otp
                        ]
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
            Log::info('✅ Interakt: otp_verification_message sent', ['response' => $result]);

            return $result;
        } catch (\Exception $e) {
            Log::error('❌ Interakt: otp_verification_message failed', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send OTP message.',
                'error' => $e->getMessage()
            ];
        }
    }
}
