<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Analytics\AnalyticsFacade as Analytics;
use Spatie\Analytics\Period;


class AnalyticsController extends Controller
{
    public function dashboard()
    {
        // Fetch analytics data for the last 7 days
        $analyticsData = Analytics::fetchVisitorsAndPageViews(Period::days(7));

        return view('admin.analytics.dashboard', compact('analyticsData'));
    }

}
