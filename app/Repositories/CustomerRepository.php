<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

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
        $term = trim($term);
        $normalized = Str::lower($term);
        $pattern = "%{$normalized}%";

        return Customer::where('shop_id', $shopId)
            ->where(function ($query) use ($pattern) {
                $query->whereRaw('LOWER(name) LIKE ?', [$pattern])
                    ->orWhereRaw('LOWER(phone) LIKE ?', [$pattern]);
            })
            ->orderByRaw('CASE
                WHEN phone = ? THEN 0
                WHEN LOWER(name) LIKE ? THEN 1
                ELSE 2
            END', [$term, "{$normalized}%"])
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
            $pattern = '%'.Str::lower(trim((string) $filters['search'])).'%';
            $query->where(function ($q) use ($pattern) {
                $q->whereRaw('LOWER(name) LIKE ?', [$pattern])
                    ->orWhereRaw('LOWER(phone) LIKE ?', [$pattern]);
            });
        }

        $perPage = min((int) ($filters['per_page'] ?? 20), 100);

        return $query->paginate($perPage);
    }
}
