<?php

namespace App\Http\Controllers;

use App\DTOs\StoreExpenseDTO;
use App\Http\Requests\Expense\ListExpenseRequest;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    public function __construct(
        private readonly ExpenseService $expenseService,
    ) {}

    public function store(StoreExpenseRequest $request): JsonResponse
    {
        try {
            $dto = StoreExpenseDTO::fromRequest($request->validated());
            $expense = $this->expenseService->createExpense($request->user(), $dto);

            return $this->created(
                new ExpenseResource($expense),
                'Expense recorded successfully'
            );
        } catch (\Throwable $e) {
            Log::error('Expense create failed', ['error' => $e->getMessage()]);

            return $this->error($e->getMessage() ?: 'Failed to record expense');
        }
    }

    public function index(ListExpenseRequest $request): JsonResponse
    {
        try {
            $filters = $request->validated();
            $expenses = $this->expenseService->listExpenses($request->user(), $filters);

            return $this->success(
                ExpenseResource::collection($expenses),
                'Expenses retrieved successfully',
                200,
                ['meta' => [
                    'current_page' => $expenses->currentPage(),
                    'last_page' => $expenses->lastPage(),
                    'per_page' => $expenses->perPage(),
                    'total' => $expenses->total(),
                ]]
            );
        } catch (\Throwable $e) {
            Log::error('Expense list failed', ['error' => $e->getMessage()]);

            return $this->error('Failed to retrieve expenses');
        }
    }
}
