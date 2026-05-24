<?php

namespace App\Repositories\Contracts;

use App\Models\Shop;

interface DashboardRepositoryInterface
{
    public function getTodaySales(Shop $shop): float;

    public function getTotalCredit(Shop $shop): float;

    public function getLowStockCount(Shop $shop): int;

    public function getTodayBillsCount(Shop $shop): int;

    public function getTodayBills(Shop $shop, int $limit = 10): iterable;

    public function getPendingSyncCount(Shop $shop): int;

    public function getCreditCustomers(Shop $shop, int $limit = 10): iterable;
}
