<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    private ReportService $report;

    public function __construct()
    {
        $this->report = new ReportService();
    }


    public function fileReports($id): JsonResponse
    {
        $totalReport = $this->report->getFileReports($id);
        return response()->json([
            'status' => true,
            'data' => $totalReport
        ]);
    }

    public function userReports(): JsonResponse
    {
        $totalReport = $this->report->getUserReports();
        return response()->json([
            'status' => true,
            'data' => $totalReport
        ]);
    }
}
