<?php

namespace App\Repositories\Contracts;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CustomerRepositoryInterface
{
    public function create(array $data): Customer;

    public function update(Customer $customer, array $data): Customer;

    public function findByUuid(string $uuid, int $shopId): ?Customer;

    public function findByUuidWithBills(string $uuid, int $shopId): ?Customer;

    public function findByPhone(string $phone, int $shopId): ?Customer;

    public function search(string $term, int $shopId, int $limit = 20): Collection;

    public function paginate(int $shopId, array $filters = []): LengthAwarePaginator;
}
