<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function findByUuid(string $uuid, int $shopId): ?Product;

    public function search(string $term, int $shopId, int $limit = 20): Collection;

    public function create(array $data): Product;

    public function paginate(int $shopId, array $filters = []): LengthAwarePaginator;

    public function update(Product $product, array $data): Product;

    public function delete(Product $product): bool;
}
