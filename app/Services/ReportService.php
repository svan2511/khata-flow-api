<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\BillingRepositoryInterface;
use App\Repositories\Contracts\ShopRepositoryInterface;

class ReportService
{
    public function __construct(
        private readonly BillingRepositoryInterface $billingRepository,
        private readonly ShopRepositoryInterface $shopRepository,
    ) {}

    public function dailyReport(User $user, ?string $date = null): array
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new \RuntimeException('Shop not found. Please setup your shop first.');
        }

        $reportDate = $date ?? today()->toDateString();

        return $this->billingRepository->getDailyReport($shop->id, $reportDate);
    }

    public function customRangeReport(User $user, string $startDate, string $endDate): array
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new \RuntimeException('Shop not found. Please setup your shop first.');
        }

        return $this->billingRepository->getCustomRangeReport($shop->id, $startDate, $endDate);
    }

    public function monthlyReport(User $user, ?int $year = null, ?int $month = null): array
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new \RuntimeException('Shop not found. Please setup your shop first.');
        }

        $year = $year ?? (int) now()->year;
        $month = $month ?? (int) now()->month;

        $currentMonth = $this->billingRepository->getMonthlyReport($shop->id, $year, $month);

        $previousMonth = $month === 1 ? 12 : $month - 1;
        $previousYear = $month === 1 ? $year - 1 : $year;
        $previousMonthData = $this->billingRepository->getMonthlyReport($shop->id, $previousYear, $previousMonth);

        $salesGrowth = $previousMonthData['total_sales'] > 0
            ? round((($currentMonth['total_sales'] - $previousMonthData['total_sales']) / $previousMonthData['total_sales']) * 100, 2)
            : ($currentMonth['total_sales'] > 0 ? 100 : 0);

        $billsGrowth = $previousMonthData['total_bills'] > 0
            ? round((($currentMonth['total_bills'] - $previousMonthData['total_bills']) / $previousMonthData['total_bills']) * 100, 2)
            : ($currentMonth['total_bills'] > 0 ? 100 : 0);

        return [
            'current_month' => $currentMonth,
            'previous_month' => $previousMonthData,
            'comparison' => [
                'sales_growth_percentage' => $salesGrowth,
                'bills_growth_percentage' => $billsGrowth,
            ],
        ];
    }
}
