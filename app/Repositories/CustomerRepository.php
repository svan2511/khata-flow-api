<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);
        return $customer->fresh();
    }

    public function findByUuid(string $uuid, int $shopId): ?Customer
    {
        return Customer::where('shop_id', $shopId)
            ->where('uuid', $uuid)
            ->first();
    }

    public function findByUuidWithBills(string $uuid, int $shopId): ?Customer
    {
        return Customer::where('shop_id', $shopId)
            ->where('uuid', $uuid)
            ->with(['bills' => function ($query) {
                $query->with(['items', 'payments'])
                    ->orderBy('id', 'desc');
            }])
            ->first();
    }

    public function findByPhone(string $phone, int $shopId): ?Customer
    {
        return Customer::where('shop_id', $shopId)
            ->where('phone', $phone)
            ->first();
    }

    public function search(string $term, int $shopId, int $limit = 20): Collection
    {
        return Customer::where('shop_id', $shopId)
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            })
            ->orderByRaw('CASE
                WHEN phone = ? THEN 0
                WHEN name LIKE ? THEN 1
                ELSE 2
            END', [$term, "{$term}%"])
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    public function paginate(int $shopId, array $filters = []): LengthAwarePaginator
    {
        $query = Customer::where('shop_id', $shopId)
            ->withCount('bills')
            ->orderBy('name');

        if (! empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            });
        }

        $perPage = min((int) ($filters['per_page'] ?? 20), 100);

        return $query->paginate($perPage);
    }
}
