<?php

namespace App\Http\Controllers;

use App\DTOs\StockInDTO;
use App\Http\Requests\Stock\StockInRequest;
use App\Http\Resources\ProductResource;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class StockController extends Controller
{
    public function __construct(
        private readonly StockService $stockService,
    ) {}

    public function stockIn(StockInRequest $request): JsonResponse
    {
        try {
            $dto = StockInDTO::fromRequest($request->validated());
            $product = $this->stockService->stockIn($request->user(), $dto);

            return $this->success(
                new ProductResource($product),
                'Stock updated successfully'
            );
        } catch (\Throwable $e) {
            Log::error('Stock in failed', ['error' => $e->getMessage()]);

            return $this->error($e->getMessage() ?: 'Failed to update stock');
        }
    }
}
