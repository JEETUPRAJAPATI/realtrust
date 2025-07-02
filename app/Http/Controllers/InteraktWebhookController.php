<?php

namespace App\Http\Controllers;

use App\Models\Property;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InteraktWebhookController extends Controller
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
        $payload = $request->all();
        $incomingSecret = $request->header('x-interakt-secret');
        $expectedSecret = env('INTERAKT_WEBHOOK_SECRET');

        // ✅ Secret Verification (optional for test payloads)
        if (!empty($incomingSecret) && $incomingSecret !== $expectedSecret) {
            Log::warning('❌ Interakt Webhook: Invalid secret', [
                'received' => $incomingSecret,
                'expected' => $expectedSecret,
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // ✅ Log full payload
        Log::info('📥 Interakt Webhook Payload Received:', $payload);

        // ✅ Handle Webhook Types
        if (!empty($payload['type'])) {
            switch ($payload['type']) {
                case 'TEMPLATE.MESSAGE_STATUS':
                    Log::info('✅ Message delivery status update', [
                        'status' => $payload['status'] ?? 'unknown',
                        'message_id' => $payload['messageId'] ?? null,
                        'phone' => $payload['phoneNumber'] ?? null,
                    ]);
                    break;

                case 'TEMPLATE.MESSAGE_DELIVERY_FAILURE':
                    Log::error('❌ Message delivery failed', [
                        'failure_reason' => $payload['failureReason'] ?? 'unknown',
                        'phone' => $payload['phoneNumber'] ?? null,
                    ]);
                    break;

                case 'USER.INCOMING_MESSAGE':
                    Log::info('💬 Incoming user message', [
                        'phone' => $payload['phoneNumber'] ?? null,
                        'message' => $payload['messageText'] ?? '',
                        'payload' => $payload['buttonPayload'] ?? null,
                    ]);


                default:
                    Log::notice('ℹ️ Unhandled Interakt webhook type', [
                        'type' => $payload['type']
                    ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Webhook processed'], 200);
    }

    public function handleUserConversation(array $payload)
    {
        $phone = $payload['phoneNumber'] ?? null;
        $message = strtolower($payload['messageText'] ?? '');
        $buttonPayload = $payload['buttonPayload'] ?? null;

        $user = UserPropertyInterest::firstOrCreate(['phone' => $phone]);

        if ($buttonPayload === 'rent' || $buttonPayload === 'sell') {
            $user->flow_type = $buttonPayload;
            $user->save();

            return $this->sendLocalityOptions($phone);
        }

        if (str_starts_with($buttonPayload, 'locality:')) {
            $locality = explode(':', $buttonPayload)[1];
            $user->locality = $locality;
            $user->save();

            return $this->sendBhkOptions($phone);
        }

        if (str_starts_with($buttonPayload, 'bhk:')) {
            $bhk = explode(':', $buttonPayload)[1];
            $user->bhk = $bhk;
            $user->save();

            return $user->flow_type === 'rent'
                ? $this->sendRentOptions($phone, $user)
                : $this->sendPriceOptions($phone, $user);
        }

        if (str_starts_with($buttonPayload, 'rent:') || str_starts_with($buttonPayload, 'price:')) {
            $user->budget = explode(':', $buttonPayload)[1];
            $user->save();

            return $this->sendTopPropertiesCarousel($phone, $user);
        }

        Log::info('❓ No matching payload found', [
            'phone' => $phone,
            'payload' => $buttonPayload,
        ]);
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
}
