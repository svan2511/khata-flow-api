<?php

namespace App\Repositories;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Payment;
use App\Repositories\Contracts\BillingRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BillingRepository implements BillingRepositoryInterface
{
    public function create(array $data): Bill
    {
        return Bill::create($data);
    }

    public function findByUuid(string $uuid, int $shopId): ?Bill
    {
        return Bill::byShop($shopId)
            ->with(['items', 'customer', 'payments'])
            ->where('uuid', $uuid)
            ->first();
    }

    public function findByBillNumber(string $billNumber, int $shopId): ?Bill
    {
        return Bill::byShop($shopId)
            ->where('bill_number', $billNumber)
            ->first();
    }

    public function getLatestBillNumber(int $shopId, string $prefix): ?string
    {
        return Bill::byShop($shopId)
            ->where('bill_number', 'like', "{$prefix}-%")
            ->orderBy('id', 'desc')
            ->value('bill_number');
    }

    public function listBills(int $shopId, array $filters = []): LengthAwarePaginator
    {
        $query = Bill::byShop($shopId)
            ->with(['customer', 'items'])
            ->orderBy('id', 'desc');

        if (! empty($filters['date_from'])) {
            $query->byDateRange(
                $filters['date_from'],
                $filters['date_to'] ?? null
            );
        }

        if (! empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (! empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        $perPage = min((int) ($filters['per_page'] ?? 20), 100);

        return $query->paginate($perPage);
    }

    public function getDailyReport(int $shopId, string $date): array
    {
        $billsQuery = Bill::byShop($shopId)
            ->whereDate('created_at', $date)
            ->where('payment_status', '!=', 'cancelled');

        $totalSales = (float) $billsQuery->sum('total');
        $totalBills = $billsQuery->count();

        $paidQuery = Bill::byShop($shopId)
            ->whereDate('created_at', $date)
            ->where('payment_status', '!=', 'cancelled')
            ->where('paid_amount', '>', 0);

        $totalPaid = (float) $paidQuery->sum('paid_amount');
        $totalDue = (float) $billsQuery->sum('due_amount');

        $averageBillValue = $totalBills > 0 ? round($totalSales / $totalBills, 2) : 0;

        $paymentBreakdown = $this->getPaymentBreakdown($shopId, $date, $date);

        $topProducts = $this->getTopProducts($shopId, $date, $date, 10);

        return [
            'date' => $date,
            'total_sales' => $totalSales,
            'total_bills' => $totalBills,
            'average_bill_value' => $averageBillValue,
            'total_paid' => $totalPaid,
            'total_due' => $totalDue,
            'payment_breakdown' => $paymentBreakdown,
            'top_products' => $topProducts,
        ];
    }

    public function getMonthlyReport(int $shopId, int $year, int $month): array
    {
        $startDate = sprintf('%d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        $billsQuery = Bill::byShop($shopId)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('payment_status', '!=', 'cancelled');

        $totalSales = (float) $billsQuery->sum('total');
        $totalBills = $billsQuery->count();

        $daysInMonth = (int) date('t', strtotime($startDate));
        $averagePerDay = $daysInMonth > 0 ? round($totalSales / $daysInMonth, 2) : 0;

        $paymentBreakdown = $this->getPaymentBreakdown($shopId, $startDate, $endDate);

        $topProducts = $this->getTopProducts($shopId, $startDate, $endDate, 10);

        $totalCredit = (float) $billsQuery->sum('due_amount');

        return [
            'month' => "{$year}-{$month}",
            'year' => $year,
            'month_number' => $month,
            'total_sales' => $totalSales,
            'total_bills' => $totalBills,
            'average_per_day' => $averagePerDay,
            'total_credit' => $totalCredit,
            'payment_breakdown' => $paymentBreakdown,
            'top_products' => $topProducts,
        ];
    }

    public function getTopProducts(int $shopId, string $startDate, string $endDate, int $limit = 10): iterable
    {
        return BillItem::select(
            'bill_items.product_id',
            'bill_items.product_name',
            DB::raw('SUM(bill_items.quantity) as total_quantity'),
            DB::raw('SUM(bill_items.total) as total_revenue'),
            DB::raw('COALESCE(products.unit, \'pc\') as unit')
        )
            ->leftJoin('products', 'bill_items.product_id', '=', 'products.id')
            ->whereHas('bill', function ($query) use ($shopId, $startDate, $endDate) {
                $query->byShop($shopId)
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->where('payment_status', '!=', 'cancelled');
            })
            ->whereNotNull('bill_items.product_id')
            ->groupBy('bill_items.product_id', 'bill_items.product_name', 'products.unit')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getPaymentBreakdown(int $shopId, string $startDate, string $endDate): array
    {
        $payments = Payment::where('shop_id', $shopId)
            ->whereDate('payment_date', '>=', $startDate)
            ->whereDate('payment_date', '<=', $endDate)
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        $creditFromBills = (float) Bill::byShop($shopId)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('payment_status', '!=', 'cancelled')
            ->sum('due_amount');

        $breakdown = [];
        foreach (['cash', 'upi', 'card', 'mix'] as $method) {
            $breakdown[$method] = (float) ($payments[$method] ?? 0);
        }
        $breakdown['credit'] = $creditFromBills;

        return $breakdown;
    }
}
