<?php

namespace App\Http\Controllers;

use App\DTOs\CustomerQuickDTO;
use App\DTOs\StoreCustomerDTO;
use App\Http\Requests\Customer\CustomerQuickRequest;
use App\Http\Requests\Customer\CustomerSearchRequest;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Resources\CustomerDetailResource;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'per_page']);
            $customers = $this->customerService->list($request->user(), $filters);

            return $this->success(
                CustomerResource::collection($customers),
                'Customers retrieved successfully',
                200,
                ['meta' => [
                    'current_page' => $customers->currentPage(),
                    'last_page' => $customers->lastPage(),
                    'per_page' => $customers->perPage(),
                    'total' => $customers->total(),
                ]]
            );
        } catch (\Throwable $e) {
            Log::error('Customer list failed', ['error' => $e->getMessage()]);

            return $this->error('Failed to retrieve customers');
        }
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        try {
            $dto = StoreCustomerDTO::fromRequest($request->validated());
            $customer = $this->customerService->createCustomer($request->user(), $dto);

            return $this->created(
                new CustomerResource($customer),
                'Customer created successfully'
            );
        } catch (\Throwable $e) {
            Log::error('Customer create failed', ['error' => $e->getMessage()]);

            return $this->error($e->getMessage() ?: 'Failed to create customer');
        }
    }

    public function show(string $uuid): JsonResponse
    {
        try {
            $customer = $this->customerService->getCustomerWithCreditHistory(
                request()->user(),
                $uuid
            );

            if (! $customer) {
                return $this->notFound('Customer not found.');
            }

            return $this->success(
                new CustomerDetailResource($customer),
                'Customer details retrieved successfully'
            );
        } catch (\Throwable $e) {
            Log::error('Customer detail fetch failed', ['uuid' => $uuid, 'error' => $e->getMessage()]);

            return $this->error('Failed to retrieve customer details');
        }
    }

    public function search(CustomerSearchRequest $request): JsonResponse
    {
        try {
            $customers = $this->customerService->search(
                $request->user(),
                $request->validated('q') ?? '',
                (int) ($request->validated('limit') ?? 20)
            );

            return $this->success(
                CustomerResource::collection($customers),
                'Customers retrieved successfully'
            );
        } catch (\Throwable $e) {
            Log::error('Customer search failed', ['error' => $e->getMessage()]);

            return $this->error('Failed to search customers');
        }
    }

    public function update(StoreCustomerRequest $request, string $uuid): JsonResponse
    {
        try {
            $dto = StoreCustomerDTO::fromRequest($request->validated());
            $customer = $this->customerService->updateCustomer($request->user(), $uuid, $dto);

            return $this->success(
                new CustomerResource($customer),
                'Customer updated successfully'
            );
        } catch (\Throwable $e) {
            Log::error('Customer update failed', ['uuid' => $uuid, 'error' => $e->getMessage()]);

            return $this->error($e->getMessage() ?: 'Failed to update customer');
        }
    }

    public function quickAdd(CustomerQuickRequest $request): JsonResponse
    {
        try {
            $dto = CustomerQuickDTO::fromRequest($request->validated());
            $customer = $this->customerService->quickAdd($request->user(), $dto);

            return $this->created(
                new CustomerResource($customer),
                'Customer created successfully'
            );
        } catch (\Throwable $e) {
            Log::error('Customer quick add failed', ['error' => $e->getMessage()]);

            return $this->error($e->getMessage() ?: 'Failed to create customer');
        }
    }
}
