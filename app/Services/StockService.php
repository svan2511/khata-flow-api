<?php

namespace App\Services;

use App\DTOs\StockInDTO;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ShopRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ShopRepositoryInterface $shopRepository,
    ) {}

    public function stockIn(User $user, StockInDTO $dto): Product
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new \RuntimeException('Shop not found. Please setup your shop first.');
        }

        $product = $this->productRepository->findByUuid($dto->productUuid, $shop->id);

        if (! $product) {
            throw new \RuntimeException('Product not found.');
        }

        return DB::transaction(function () use ($product, $dto): Product {
            $updateData = [];

            $updateData['stock_quantity'] = $product->stock_quantity + $dto->quantity;

            if ($dto->costPrice !== null) {
                $updateData['cost_price'] = $dto->costPrice;
            }

            $this->productRepository->update($product, $updateData);

            Log::info('Stock In recorded', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity_added' => $dto->quantity,
                'new_quantity' => $updateData['stock_quantity'],
                'cost_price' => $dto->costPrice,
                'notes' => $dto->notes,
            ]);

            return $product->fresh();
        });
    }
}
