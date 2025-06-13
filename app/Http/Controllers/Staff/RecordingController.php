<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\KnowlarityService;
use Illuminate\Http\Request;

class RecordingController extends Controller
{
    protected $knowlarityService;

    public function __construct(KnowlarityService $knowlarityService)
    {
        $this->knowlarityService = $knowlarityService;
    }

    public function showRecordings(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $recordings = $this->knowlarityService->getCallRecordings($startDate, $endDate);

        if (isset($recordings['error'])) {
            return back()->with('error', $recordings['error']);
        }

        return view('recordings.index', compact('recordings'));
    }
}
