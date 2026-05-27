<?php

namespace App\Repositories;

use App\Models\Bill;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shop;
use App\Repositories\Contracts\DashboardRepositoryInterface;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getTodaySales(Shop $shop): float
    {
        return (float) Bill::byShop($shop->id)
            ->whereDate('created_at', today())
            ->where('payment_status', '!=', 'cancelled')
            ->sum('total');
    }

    public function getTodayCredit(Shop $shop): float
    {
        return (float) Bill::byShop($shop->id)
            ->whereDate('created_at', today())
            ->where('payment_status', '!=', 'cancelled')
            ->sum('due_amount');
    }

    public function getTodayCash(Shop $shop): float
    {
        return (float) Payment::where('shop_id', $shop->id)
            ->whereDate('payment_date', today())
            ->sum('amount');
    }

    public function getTotalCredit(Shop $shop): float
    {
        return (float) Customer::where('shop_id', $shop->id)
            ->sum('total_credit');
    }

    public function getLowStockCount(Shop $shop): int
    {
        return Product::byShop($shop->id)
            ->lowStock()
            ->count();
    }

    public function getTodayBillsCount(Shop $shop): int
    {
        return Bill::byShop($shop->id)
            ->whereDate('created_at', today())
            ->count();
    }

    public function getTodayBills(Shop $shop, int $limit = 10): iterable
    {
        return Bill::byShop($shop->id)
            ->whereDate('created_at', today())
            ->with(['customer', 'items'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getCreditCustomers(Shop $shop, int $limit = 10): iterable
    {
        return Customer::where('shop_id', $shop->id)
            ->where('total_credit', '>', 0)
            ->orderByDesc('total_credit')
            ->limit($limit)
            ->get();
    }

    public function getPendingSyncCount(Shop $shop): int
    {
        return 0;
    }
}
