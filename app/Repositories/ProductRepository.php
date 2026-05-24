<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function findByUuid(string $uuid, int $shopId): ?Product
    {
        return Product::byShop($shopId)
            ->active()
            ->where('uuid', $uuid)
            ->first();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh();
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    public function search(string $term, int $shopId, int $limit = 20): Collection
    {
        return Product::byShop($shopId)
            ->active()
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('barcode', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%");
            })
            ->orderByRaw('CASE
                WHEN barcode = ? THEN 0
                WHEN name LIKE ? THEN 1
                ELSE 2
            END', [$term, "{$term}%"])
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    public function paginate(int $shopId, array $filters = []): LengthAwarePaginator
    {
        $query = Product::byShop($shopId)
            ->with('category')
            ->orderBy('name');

        if (! empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('barcode', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%");
            });
        }

        if (! empty($filters['category_id'])) {
            $query->where('product_category_id', $filters['category_id']);
        }

        if (! empty($filters['low_stock'])) {
            $query->lowStock();
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = min((int) ($filters['per_page'] ?? 20), 100);

        return $query->paginate($perPage);
    }
}
