<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductRepository implements ProductRepositoryInterface
{
    public function findByUuid(string $uuid, int $shopId): ?Product
    {
        return Product::byShop($shopId)
            ->active()
            ->with('category')
            ->where('uuid', $uuid)
            ->first();
    }

    public function allActive(int $shopId, int $limit = 2000): Collection
    {
        return Product::byShop($shopId)
            ->active()
            ->orderBy('name')
            ->limit($limit)
            ->get();
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
        $term = trim($term);
        $normalized = Str::lower($term);
        $pattern = "%{$normalized}%";

        $version = (int) Cache::get("product:version:{$shopId}", 0);
        $cacheKey = "product:search:{$shopId}:{$version}:{$normalized}:{$limit}";

        return Cache::remember($cacheKey, 60, function () use ($normalized, $pattern, $shopId, $limit) {
            return Product::byShop($shopId)
                ->active()
                ->where(function ($query) use ($pattern) {
                    $query->whereRaw('LOWER(name) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(barcode) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(sku) LIKE ?', [$pattern]);
                })
                ->orderByRaw('CASE
                    WHEN LOWER(barcode) = ? THEN 0
                    WHEN LOWER(name) LIKE ? THEN 1
                    ELSE 2
                END', [$normalized, "{$normalized}%"])
                ->orderBy('name')
                ->limit($limit)
                ->get();
        });
    }

    public function paginate(int $shopId, array $filters = []): LengthAwarePaginator
    {
        $query = Product::byShop($shopId)
            ->with('category')
            ->orderBy('name');

        if (! empty($filters['search'])) {
            $pattern = '%'.Str::lower(trim((string) $filters['search'])).'%';
            $query->where(function ($q) use ($pattern) {
                $q->whereRaw('LOWER(name) LIKE ?', [$pattern])
                    ->orWhereRaw('LOWER(barcode) LIKE ?', [$pattern])
                    ->orWhereRaw('LOWER(sku) LIKE ?', [$pattern]);
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
