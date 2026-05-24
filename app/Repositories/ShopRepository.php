<?php

namespace App\Repositories;

use App\Models\Shop;
use App\Models\User;
use App\Repositories\Contracts\ShopRepositoryInterface;

class ShopRepository implements ShopRepositoryInterface
{
    public function findById(int $id): ?Shop
    {
        return Shop::find($id);
    }

    public function findByUuid(string $uuid): ?Shop
    {
        return Shop::where('uuid', $uuid)->first();
    }

    public function findBySlug(string $slug): ?Shop
    {
        return Shop::where('shop_slug', $slug)->first();
    }

    public function findByUser(User $user): ?Shop
    {
        return Shop::where('user_id', $user->id)->first();
    }

    public function create(array $data): Shop
    {
        return Shop::create($data);
    }

    public function update(Shop $shop, array $data): Shop
    {
        $shop->update($data);

        return $shop->fresh();
    }

    public function delete(Shop $shop): bool
    {
        return $shop->delete();
    }
}
