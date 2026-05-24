<?php

namespace App\Repositories\Contracts;

use App\Models\Shop;
use App\Models\User;

interface ShopRepositoryInterface
{
    public function findById(int $id): ?Shop;

    public function findByUuid(string $uuid): ?Shop;

    public function findBySlug(string $slug): ?Shop;

    public function findByUser(User $user): ?Shop;

    public function create(array $data): Shop;

    public function update(Shop $shop, array $data): Shop;

    public function delete(Shop $shop): bool;
}
