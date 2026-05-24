<?php

namespace App\Http\Controllers;

use App\Http\Resources\SyncStatusResource;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SyncController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {}

    public function status(): JsonResponse
    {
        try {
            $syncStatus = $this->dashboardService->getSyncStatus(request()->user());

            return $this->success(
                data: new SyncStatusResource($syncStatus),
                message: 'Sync status retrieved successfully.',
            );
        } catch (\Exception $e) {
            Log::error('Sync status fetch failed', ['error' => $e->getMessage()]);

            return $this->error('Failed to fetch sync status.', 500);
        }
    }
}
