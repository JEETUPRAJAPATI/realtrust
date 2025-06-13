<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $client;
    protected $phoneNumberId;
    protected $token;

    public function __construct()
    {
        $this->client = new Client();
        $this->phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
        $this->token = env('WHATSAPP_TOKEN');
    }

    public function sendTemplateMessage($phoneNumber, $templateName, $languageCode)
    {
        $url = env('WHATSAPP_PHONE_NUMBER_ID') . '/messages';
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phoneNumber,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $languageCode],
                'components' => [
                    [
                        'type' => 'body'
                    ]
                ]
            ]
        ];
        // dd($payload);-s
        $response = $this->client->post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . env('WHATSAPP_TOKEN'),
                'Content-Type' => 'application/json',
            ],
            'json' => $payload
        ]);

        return json_decode($response->getBody(), true);
    }

    // public function sendImageTemplateMessage($phoneNumber, $templateName, $languageCode, $variables)
    // {
    //     $url = "https://graph.facebook.com/v21.0/{$this->phoneNumberId}/messages";
    //     $payload = [
    //         "messaging_product" => "whatsapp",
    //         "recipient_type" => "individual",
    //         "to" => $phoneNumber,
    //         "type" => "template",
    //         "template" => [
    //             "name" => $templateName,
    //             "language" => [
    //                 "code" => $languageCode
    //             ],
    //             "components" => [
    //                 [
    //                     "type" => "header",
    //                     "parameters" => [
    //                         [
    //                             "type" => "image",
    //                             "image" => [
    //                                 "link" => "https://letsenhance.io/static/8f5e523ee6b2479e26ecc91b9c25261e/1015f/MainAfter.jpg"
    //                             ]
    //                         ]
    //                     ]
    //                 ],
    //                 [
    //                     "type" => "body",
    //                     "parameters" => [
    //                         [
    //                             "type" => "text",
    //                             "text" => $variables
    //                         ]
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ];
    //     try {
    //         $response = $this->client->post($url, [
    //             'json' => $payload,
    //             'headers' => [
    //                 'Authorization' => 'Bearer ' . $this->token,
    //                 'Content-Type' => 'application/json'
    //             ]
    //         ]);
    //         return json_decode($response->getBody(), true);
    //     } catch (RequestException $e) {
    //         return [
    //             'error' => true,
    //             'message' => $e->getMessage()
    //         ];
    //     }
    // }

    public function sendImageTemplateMessage($phoneNumber, $templateName, $languageCode, $variables, $confirmationUrl, $image)
    {
        $url = "https://graph.facebook.com/v22.0/{$this->phoneNumberId}/messages";
        $components = [
            [
                "type" => "header",
                "parameters" => [
                    [
                        "type" => "image",
                        "image" => [
                            "link" => $image
                        ]
                    ]
                ]
            ],
            [
                "type" => "body",
                "parameters" => []
            ],
            [
                "type" => "button",
                "sub_type" => "url",
                "index" => "0",
                "parameters" => [
                    [
                        "type" => "text",
                        "text" => $confirmationUrl
                    ]
                ]
            ]
        ];

        // Map variables to the body parameters
        foreach ($variables as $index => $variable) {
            $components[1]['parameters'][] = [
                "type" => "text",
                "text" => $variable
            ];
        }

        $payload = [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $phoneNumber,
            "type" => "template",
            "template" => [
                "name" => $templateName,
                "language" => [
                    "code" => $languageCode
                ],
                "components" => $components
            ]
        ];
        // dd($payload);
        try {
            $response = $this->client->post($url, [
                'json' => $payload,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json'
                ]
            ]);
            Log::info('------------------START-------------------------');
            Log::info('Response Status: ' . $response->getStatusCode());
            Log::info('Response Body: ' . $response->getBody()->getContents());
            Log::info('URL: ' . $url);
            Log::info('-------------------END------------------------');
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }


    public function sendOtpTemplateMessage($phoneNumber, $templateName, $languageCode, $variables)
    {

        $url = "https://graph.facebook.com/v22.0/{$this->phoneNumberId}/messages";
        $payload = [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $phoneNumber,
            "type" => "template",
            "template" => [
                "name" => $templateName,
                "language" => [
                    "code" => $languageCode,
                ],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $variables,
                            ],
                        ],
                    ],
                    [
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => "0",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => 'fgg',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        try {
            $response = $this->client->post($url, [
                'json' => $payload,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json'
                ]
            ]);
            $responseData = json_decode($response->getBody(), true);
            // Check if the response contains any errors from the API
            Log::info('------------------START-------------------------');
            Log::info('Response Status: ' . $response->getStatusCode());
            Log::info('Response Body: ' . $response->getBody()->getContents());
            Log::info('URL: ' . $url);
            Log::info('-------------------END------------------------');
            if (isset($responseData['error'])) {
                return [
                    'error' => true,
                    'message' => $responseData['error']['message'],
                    'error_code' => $responseData['error']['code'] ?? null,
                ];
            }

            return $responseData;
        } catch (RequestException $e) {
            // Log the error for debugging purposes
            Log::error('WhatsApp API error: ' . $e->getMessage());

            // Handle different response scenarios
            if ($e->hasResponse()) {
                $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);
                return [
                    'error' => true,
                    'message' => $responseBody['error']['message'] ?? 'An error occurred while sending the message.',
                    'error_code' => $responseBody['error']['code'] ?? null,
                ];
            }

            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }


    public function sendingWhatsAppMessageToFieldManager($phoneNumber, $templateName, $languageCode, $variables)
    {
        $url = "https://graph.facebook.com/v22.0/{$this->phoneNumberId}/messages";
        $payload = [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $phoneNumber,
            "type" => "template",
            "template" => [
                "name" => $templateName,
                "language" => [
                    "code" => $languageCode,
                ],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => array_map(function ($variable) {
                            return ["type" => "text", "text" => $variable];
                        }, $variables),
                    ],
                ],
            ],
        ];

        // dd($payload);
        try {
            $response = $this->client->post($url, [
                'json' => $payload,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json'
                ]
            ]);
            $responseData = json_decode($response->getBody(), true);
            Log::info('------------------START-------------------------');
            Log::info('Response Status: ' . $response->getStatusCode());
            Log::info('Response Body: ' . $response->getBody()->getContents());
            Log::info('URL: ' . $url);
            Log::info('-------------------END------------------------');
            // Check if the response contains any errors from the API
            if (isset($responseData['error'])) {
                return [
                    'error' => true,
                    'message' => $responseData['error']['message'],
                    'error_code' => $responseData['error']['code'] ?? null,
                ];
            }

            return $responseData;
        } catch (RequestException $e) {
            // Log the error for debugging purposes
            Log::error('WhatsApp API error: ' . $e->getMessage());

            // Handle different response scenarios
            if ($e->hasResponse()) {
                $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);
                return [
                    'error' => true,
                    'message' => $responseBody['error']['message'] ?? 'An error occurred while sending the message.',
                    'error_code' => $responseBody['error']['code'] ?? null,
                ];
            }

            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }


    public function sendingWhatsAppMessageToUser($phoneNumber, $templateName, $languageCode, $variables, $confirmationUrl, $image, $callingUrl)
    {
        $url = "https://graph.facebook.com/v22.0/{$this->phoneNumberId}/messages";
        $payload = [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $phoneNumber,
            "type" => "template",
            "template" => [
                "name" => $templateName,
                "language" => [
                    "code" => $languageCode,
                ],
                "components" => [
                    [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type" => "image",  // Type of component is image
                                "image" => [
                                    "link" => $image  // Image URL
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "body",
                        "parameters" => array_map(function ($variable) {
                            return ["type" => "text", "text" => $variable];
                        }, $variables),
                    ],
                    // Button 1: URL Button for Confirmation
                    [
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => "0",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $confirmationUrl
                            ]
                        ]
                    ],
                    // Button 2: Call Button for WhatsApp Calling
                    [
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => "1",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $callingUrl
                            ]
                        ]
                    ]
                ],

            ],
        ];
        // dd($payload);
        Log::info('------------------START-------------------------');
        Log::info($payload);
        Log::info($url);
        Log::info('-------------------END------------------------');
        try {
            $response = $this->client->post($url, [
                'json' => $payload,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json'
                ]
            ]);
            $responseData = json_decode($response->getBody(), true);

            Log::info('------------------RESPONSE START-------------------------');
            Log::info($responseData);
            Log::info('-------------------RESPONSE END------------------------');
            // Check if the response contains any errors from the API
            if (isset($responseData['error'])) {
                return [
                    'error' => true,
                    'message' => $responseData['error']['message'],
                    'error_code' => $responseData['error']['code'] ?? null,
                ];
            }

            return $responseData;
        } catch (RequestException $e) {
            // Log the error for debugging purposes
            Log::error('WhatsApp API error: ' . $e->getMessage());

            // Handle different response scenarios
            if ($e->hasResponse()) {
                $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);
                return [
                    'error' => true,
                    'message' => $responseBody['error']['message'] ?? 'An error occurred while sending the message.',
                    'error_code' => $responseBody['error']['code'] ?? null,
                ];
            }

            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }



    public function sendConformationForm($phoneNumber, $templateName, $languageCode, $variables, $confirmationUrl, $image, $secondButtonUrl)
    {
        $url = "https://graph.facebook.com/v22.0/{$this->phoneNumberId}/messages";
        $payload = [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $phoneNumber,
            "type" => "template",
            "template" => [
                "name" => $templateName,
                "language" => [
                    "code" => $languageCode,
                ],
                "components" => [
                    [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type" => "image",
                                "image" => [
                                    "link" => $image
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "body",
                        "parameters" => array_map(function ($variable) {
                            return ["type" => "text", "text" => $variable];
                        }, $variables),
                    ],
                    [
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => "0",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $confirmationUrl
                            ]
                        ]
                    ],
                    [
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => "1",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $secondButtonUrl
                            ]
                        ]
                    ]
                ],
            ],
        ];

        Log::info('Sending WhatsApp message', [
            'payload' => $payload,
            'url' => $url
        ]);

        try {
            $response = $this->client->post($url, [
                'json' => $payload,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $responseData = json_decode($response->getBody(), true);

            Log::info('WhatsApp API response', ['response' => $responseData]);

            if (isset($responseData['error'])) {
                return [
                    'error' => true,
                    'message' => $responseData['error']['message'] ?? 'Unknown error',
                    'error_code' => $responseData['error']['code'] ?? null,
                ];
            }

            return $responseData;
        } catch (RequestException $e) {
            Log::error('WhatsApp API error', ['exception' => $e->getMessage()]);

            if ($e->hasResponse()) {
                $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);
                return [
                    'error' => true,
                    'message' => $responseBody['error']['message'] ?? 'An error occurred while sending the message.',
                    'error_code' => $responseBody['error']['code'] ?? null,
                ];
            }

            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function sendCongratsMessageOwner($phoneNumber, $templateName, $languageCode, $variables)
    {
        $url = "https://graph.facebook.com/v21.0/{$this->phoneNumberId}/messages";

        $payload = [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $phoneNumber,
            "type" => "template",
            "template" => [
                "name" => $templateName,
                "language" => ["code" => $languageCode],
                "components" => [[
                    "type" => "body",
                    "parameters" => array_map(fn($v) => ["type" => "text", "text" => $v], $variables),
                ]],
            ],
        ];

        try {
            $response = $this->client->post($url, [
                'json' => $payload,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json'
                ]
            ]);

            $responseData = json_decode($response->getBody(), true);

            Log::info('WhatsApp Response:', [
                'status' => $response->getStatusCode(),
                'body' => $responseData,
                'url' => $url
            ]);

            if (isset($responseData['error'])) {
                return [
                    'error' => true,
                    'message' => $responseData['error']['message'],
                    'error_code' => $responseData['error']['code'] ?? null,
                ];
            }

            return $responseData;
        } catch (RequestException $e) {
            Log::error('WhatsApp API error', ['message' => $e->getMessage()]);

            $responseBody = $e->hasResponse() ? json_decode($e->getResponse()->getBody()->getContents(), true) : [];

            return [
                'error' => true,
                'message' => $responseBody['error']['message'] ?? $e->getMessage(),
                'error_code' => $responseBody['error']['code'] ?? null,
            ];
        }
    }
}
