<?php

namespace App\Http\Controllers;

use App\DTOs\ShopSetupDTO;
use App\Http\Requests\ShopSetupRequest;
use App\Http\Resources\ShopResource;
use App\Services\ShopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{
    public function __construct(
        private readonly ShopService $shopService,
    ) {}

    public function setup(ShopSetupRequest $request): JsonResponse
    {
        try {
            $dto = ShopSetupDTO::fromRequest($request->validated() + $request->allFiles());
            $shop = $this->shopService->setup($request->user(), $dto);

            return $this->success(
                data: new ShopResource($shop),
                message: 'Shop setup completed successfully.',
            );
        } catch (\Exception $e) {
            Log::error('Shop setup failed', ['error' => $e->getMessage()]);

            return $this->error('Shop setup failed. Please try again.', 500);
        }
    }
}
