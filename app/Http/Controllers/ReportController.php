<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomRangeReportResource;
use App\Http\Resources\DailyReportResource;
use App\Http\Resources\MonthlyReportResource;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
    ) {}

    public function daily(Request $request): JsonResponse
    {
        try {
            $date = $request->query('date', today()->toDateString());
            $report = $this->reportService->dailyReport($request->user(), $date);

            return $this->success(
                new DailyReportResource($report),
                'Daily report retrieved successfully'
            );
        } catch (\Throwable $e) {
            Log::error('Daily report failed', ['error' => $e->getMessage()]);

            return $this->error($e->getMessage() ?: 'Failed to generate daily report');
        }
    }

    public function customRange(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $report = $this->reportService->customRangeReport(
                $request->user(),
                $request->query('start_date'),
                $request->query('end_date')
            );

            return $this->success(
                new CustomRangeReportResource($report),
                'Custom range report retrieved successfully'
            );
        } catch (\Throwable $e) {
            Log::error('Custom range report failed', ['error' => $e->getMessage()]);

            return $this->error($e->getMessage() ?: 'Failed to generate custom range report');
        }
    }

    public function monthly(Request $request): JsonResponse
    {
        try {
            $year = $request->query('year') ? (int) $request->query('year') : null;
            $month = $request->query('month') ? (int) $request->query('month') : null;
            $report = $this->reportService->monthlyReport($request->user(), $year, $month);

            return $this->success(
                new MonthlyReportResource($report),
                'Monthly report retrieved successfully'
            );
        } catch (\Throwable $e) {
            Log::error('Monthly report failed', ['error' => $e->getMessage()]);

            return $this->error($e->getMessage() ?: 'Failed to generate monthly report');
        }
    }
}
