<?php

namespace App\Repositories\Contracts;

use App\Models\Expense;
use Illuminate\Pagination\LengthAwarePaginator;

interface ExpenseRepositoryInterface
{
    public function create(array $data): Expense;

    public function paginate(int $shopId, array $filters = []): LengthAwarePaginator;
}
