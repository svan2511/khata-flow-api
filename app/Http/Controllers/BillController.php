<?php

namespace App\Http\Controllers;

use App\DTOs\CreateBillDTO;
use App\DTOs\PaymentDTO;
use App\Exceptions\BillingException;
use App\Exceptions\InsufficientStockException;
use App\Http\Requests\Bill\ListBillRequest;
use App\Http\Requests\Bill\StoreBillRequest;
use App\Http\Requests\Bill\StorePaymentRequest;
use App\Http\Resources\BillListResource;
use App\Http\Resources\BillResource;
use App\Services\BillService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BillController extends Controller
{
    public function __construct(
        private readonly BillService $billService,
    ) {}

    public function store(StoreBillRequest $request): JsonResponse
    {
        try {
            $dto = CreateBillDTO::fromRequest(
                $request->validated(),
                $request->user()->id
            );

            $bill = $this->billService->createBill($request->user(), $dto);

            return $this->created(
                new BillResource($bill),
                'Bill created successfully'
            );
        } catch (InsufficientStockException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (BillingException $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        } catch (\Throwable $e) {
            Log::error('Bill creation failed', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->error('Failed to create bill. Please try again.');
        }
    }

    public function index(ListBillRequest $request): JsonResponse
    {
        try {
            $filters = $request->validated();
            $bills = $this->billService->listBills($request->user(), $filters);

            return $this->success(
                BillListResource::collection($bills),
                'Bills retrieved successfully',
                200,
                [
                    'meta' => [
                        'current_page' => $bills->currentPage(),
                        'last_page' => $bills->lastPage(),
                        'per_page' => $bills->perPage(),
                        'total' => $bills->total(),
                    ],
                ]
            );
        } catch (BillingException $e) {
            return $this->error($e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Bill list failed', ['error' => $e->getMessage()]);

            return $this->error('Failed to retrieve bills');
        }
    }

    public function show(string $uuid): JsonResponse
    {
        try {
            $bill = $this->billService->getBill(request()->user(), $uuid);

            if (! $bill) {
                return $this->notFound('Bill not found');
            }

            return $this->success(
                new BillResource($bill),
                'Bill retrieved successfully'
            );
        } catch (\Throwable $e) {
            Log::error('Bill fetch failed', ['uuid' => $uuid, 'error' => $e->getMessage()]);

            return $this->error('Failed to retrieve bill');
        }
    }

    public function addPayment(StorePaymentRequest $request, string $uuid): JsonResponse
    {
        try {
            $dto = PaymentDTO::fromRequest($request->validated());
            $bill = $this->billService->addPayment($request->user(), $uuid, $dto);

            return $this->success(
                new BillResource($bill),
                'Payment added successfully'
            );
        } catch (BillingException $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        } catch (\Throwable $e) {
            Log::error('Payment addition failed', [
                'bill_uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);

            return $this->error('Failed to add payment');
        }
    }
}
