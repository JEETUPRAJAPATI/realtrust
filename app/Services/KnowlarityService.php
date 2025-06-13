<?php

namespace App\Services;

use GuzzleHttp\Client;

class KnowlarityService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client(config: ['base_uri' => 'https://kpi.knowlarity.com']);
        $this->apiKey = env('KNOWLARITY_API_KEY');
    }

    public function getCallRecordings($startDate, $endDate)
    {
        try {
            $response = $this->client->get('/v1/call/recordings', [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'start_date' => $startDate, // Format: YYYY-MM-DD
                    'end_date' => $endDate,    // Format: YYYY-MM-DD
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
