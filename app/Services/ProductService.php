<?php

namespace App\Services;

use App\DTOs\ProductQuickDTO;
use App\DTOs\StoreProductDTO;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ShopRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ShopRepositoryInterface $shopRepository,
        private readonly CloudinaryService $cloudinaryService,
    ) {}

    public function search(User $user, string $term, int $limit = 20): Collection
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            return collect();
        }

        return $this->productRepository->search($term, $shop->id, $limit);
    }

    public function quickAdd(User $user, ProductQuickDTO $dto): Product
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new \RuntimeException('Shop not found. Please setup your shop first.');
        }

        return $this->productRepository->create([
            'shop_id' => $shop->id,
            'name' => $dto->name,
            'price' => $dto->price,
            'unit' => $dto->unit,
            'stock_quantity' => 0,
            'is_active' => true,
        ]);
    }

    public function getProduct(User $user, string $uuid): ?Product
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            return null;
        }

        return $this->productRepository->findByUuid($uuid, $shop->id);
    }

    public function list(User $user, array $filters = []): LengthAwarePaginator
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new \RuntimeException('Shop not found. Please setup your shop first.');
        }

        return $this->productRepository->paginate($shop->id, $filters);
    }

    public function createProduct(User $user, StoreProductDTO $dto): Product
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new \RuntimeException('Shop not found. Please setup your shop first.');
        }

        return DB::transaction(function () use ($shop, $dto): Product {
            $productData = $dto->toArray();
            $productData['shop_id'] = $shop->id;

            if ($dto->categoryName) {
                $category = ProductCategory::firstOrCreate(
                    ['shop_id' => $shop->id, 'name' => $dto->categoryName],
                    ['is_active' => true]
                );
                $productData['product_category_id'] = $category->id;
            }

            if ($dto->hasImage()) {
                $imageData = $this->cloudinaryService->upload(
                    $dto->image,
                    'dukaan-sahayak/products'
                );

                if ($imageData) {
                    $productData['image'] = $imageData['url'];
                    $productData['cloudinary_public_id'] = $imageData['public_id'];
                }
            }

            Log::info('Product created', [
                'shop_id' => $shop->id,
                'name' => $dto->name,
            ]);

            return $this->productRepository->create($productData);
        });
    }

    public function updateProduct(User $user, string $uuid, StoreProductDTO $dto): Product
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new \RuntimeException('Shop not found. Please setup your shop first.');
        }

        $product = $this->productRepository->findByUuid($uuid, $shop->id);

        if (! $product) {
            throw new \RuntimeException('Product not found.');
        }

        return DB::transaction(function () use ($shop, $product, $dto): Product {
            $productData = $dto->toArray();
            $productData = array_filter($productData, fn ($value) => ! is_null($value));

            if ($dto->categoryName) {
                $category = ProductCategory::firstOrCreate(
                    ['shop_id' => $shop->id, 'name' => $dto->categoryName],
                    ['is_active' => true]
                );
                $productData['product_category_id'] = $category->id;
            }

            if ($dto->hasImage()) {
                if ($product->cloudinary_public_id) {
                    $this->cloudinaryService->delete($product->cloudinary_public_id);
                }

                $imageData = $this->cloudinaryService->upload(
                    $dto->image,
                    'dukaan-sahayak/products'
                );

                if ($imageData) {
                    $productData['image'] = $imageData['url'];
                    $productData['cloudinary_public_id'] = $imageData['public_id'];
                }
            }

            Log::info('Product updated', [
                'product_id' => $product->id,
                'name' => $dto->name ?? $product->name,
            ]);

            return $this->productRepository->update($product, $productData);
        });
    }

    public function deleteProduct(User $user, string $uuid): void
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new \RuntimeException('Shop not found. Please setup your shop first.');
        }

        $product = $this->productRepository->findByUuid($uuid, $shop->id);

        if (! $product) {
            throw new \RuntimeException('Product not found.');
        }

        DB::transaction(function () use ($product): void {
            if ($product->cloudinary_public_id) {
                $this->cloudinaryService->delete($product->cloudinary_public_id);
            }

            $this->productRepository->delete($product);

            Log::info('Product deleted', [
                'product_id' => $product->id,
                'name' => $product->name,
            ]);
        });
    }
}
