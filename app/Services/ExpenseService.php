<?php

namespace App\Services;

use App\DTOs\StoreExpenseDTO;
use App\Models\User;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Repositories\Contracts\ShopRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ExpenseService
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $expenseRepository,
        private readonly ShopRepositoryInterface $shopRepository,
    ) {}

    public function createExpense(User $user, StoreExpenseDTO $dto): \App\Models\Expense
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new \RuntimeException('Shop not found. Please setup your shop first.');
        }

        $expense = $this->expenseRepository->create([
            'shop_id' => $shop->id,
            'title' => $dto->title,
            'amount' => $dto->amount,
            'category' => $dto->category,
            'payment_method' => $dto->paymentMethod ?? 'cash',
            'expense_date' => $dto->expenseDate,
            'notes' => $dto->notes,
        ]);

        Log::info('Expense created', [
            'shop_id' => $shop->id,
            'title' => $dto->title,
            'amount' => $dto->amount,
        ]);

        return $expense;
    }

    public function listExpenses(User $user, array $filters = []): LengthAwarePaginator
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new \RuntimeException('Shop not found. Please setup your shop first.');
        }

        return $this->expenseRepository->paginate($shop->id, $filters);
    }
}
