<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\DashboardRepositoryInterface;

class DashboardService
{
    public function __construct(
        private readonly DashboardRepositoryInterface $dashboardRepository,
        private readonly ShopService $shopService,
    ) {}

    public function getDashboard(User $user): array
    {
        $shop = $this->shopService->getShopByUser($user);

        if (! $shop) {
            return [
                'today_sales' => 0,
                'total_credit' => 0,
                'low_stock_count' => 0,
                'today_bills_count' => 0,
                'today_bills' => [],
                'has_shop' => false,
            ];
        }

        return [
            'today_sales' => $this->dashboardRepository->getTodaySales($shop),
            'total_credit' => $this->dashboardRepository->getTotalCredit($shop),
            'low_stock_count' => $this->dashboardRepository->getLowStockCount($shop),
            'today_bills_count' => $this->dashboardRepository->getTodayBillsCount($shop),
            'today_bills' => $this->dashboardRepository->getTodayBills($shop),
            'credit_customers' => $this->dashboardRepository->getCreditCustomers($shop, 10),
            'has_shop' => true,
        ];
    }

    public function getSyncStatus(User $user): array
    {
        $shop = $this->shopService->getShopByUser($user);

        return [
            'is_synced' => true,
            'pending_count' => $shop ? $this->dashboardRepository->getPendingSyncCount($shop) : 0,
            'last_synced_at' => now()->toIso8601String(),
        ];
    }
}
