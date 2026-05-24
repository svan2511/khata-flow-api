<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardResource;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {}

    public function index(): JsonResponse
    {
        try {
            $dashboard = $this->dashboardService->getDashboard(request()->user());

            return $this->success(
                data: new DashboardResource($dashboard),
                message: 'Dashboard data retrieved successfully.',
            );
        } catch (\Exception $e) {
            Log::error('Dashboard fetch failed', ['error' => $e->getMessage()]);

            return $this->error('Failed to fetch dashboard data.', 500);
        }
    }
}
