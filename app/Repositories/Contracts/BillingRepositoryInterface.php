<?php

namespace App\Repositories\Contracts;

use App\Models\Bill;
use Illuminate\Pagination\LengthAwarePaginator;

interface BillingRepositoryInterface
{
    public function create(array $data): Bill;

    public function findByUuid(string $uuid, int $shopId): ?Bill;

    public function findByBillNumber(string $billNumber, int $shopId): ?Bill;

    public function getLatestBillNumber(int $shopId, string $prefix): ?string;

    public function listBills(int $shopId, array $filters = []): LengthAwarePaginator;

    public function getDailyReport(int $shopId, string $date): array;

    public function getMonthlyReport(int $shopId, int $year, int $month): array;

    public function getTopProducts(int $shopId, string $startDate, string $endDate, int $limit = 10): iterable;

    public function getPaymentBreakdown(int $shopId, string $startDate, string $endDate): array;
}
