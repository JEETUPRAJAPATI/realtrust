<?php

namespace App\Services;

use Google_Client;
use Google_Service_Analytics;

class GoogleAnalyticsService
{
    protected $client;
    protected $analytics;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(public_path('google-analytics.json'));
        $this->client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);

        $this->analytics = new Google_Service_Analytics($this->client);
    }

    public function getTotalVisits($viewId, $startDate, $endDate)
    {
        $results = $this->analytics->data_ga->get(
            'ga:' . $viewId,
            $startDate,
            $endDate,
            'ga:sessions'
        );

        return $results->getRows()[0][0];
    }

    public function getMostVisitedPages($viewId, $startDate, $endDate, $limit = 10)
    {
        $results = $this->analytics->data_ga->get(
            'ga:' . $viewId,
            $startDate,
            $endDate,
            'ga:pageviews',
            [
                'dimensions' => 'ga:pagePath',
                'sort' => '-ga:pageviews',
                'max-results' => $limit
            ]
        );

        return $results->getRows();
    }
}
