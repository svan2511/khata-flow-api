<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function create(array $data): Expense
    {
        return Expense::create($data);
    }

    public function paginate(int $shopId, array $filters = []): LengthAwarePaginator
    {
        $query = Expense::byShop($shopId)
            ->orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc');

        if (! empty($filters['date_from'])) {
            $query->byDateRange(
                $filters['date_from'],
                $filters['date_to'] ?? null
            );
        }

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        $perPage = min((int) ($filters['per_page'] ?? 20), 100);

        return $query->paginate($perPage);
    }
}
