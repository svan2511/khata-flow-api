<?php

namespace App\Services;

use App\DTOs\CustomerQuickDTO;
use App\DTOs\StoreCustomerDTO;
use App\Models\Customer;
use App\Models\User;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\ShopRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly ShopRepositoryInterface $shopRepository,
    ) {}

    public function search(User $user, string $term, int $limit = 20): Collection
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            return collect();
        }

        return $this->customerRepository->search($term, $shop->id, $limit);
    }

    public function quickAdd(User $user, CustomerQuickDTO $dto): Customer
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new \RuntimeException('Shop not found. Please setup your shop first.');
        }

        if ($dto->phone) {
            $existing = $this->customerRepository->findByPhone($dto->phone, $shop->id);

            if ($existing) {
                return $existing;
            }
        }

        return $this->customerRepository->create([
            'shop_id' => $shop->id,
            'name' => $dto->name,
            'phone' => $dto->phone,
            'email' => $dto->email,
            'address' => $dto->address,
        ]);
    }

    public function list(User $user, array $filters = []): LengthAwarePaginator
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new \RuntimeException('Shop not found. Please setup your shop first.');
        }

        return $this->customerRepository->paginate($shop->id, $filters);
    }

    public function createCustomer(User $user, StoreCustomerDTO $dto): Customer
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new \RuntimeException('Shop not found. Please setup your shop first.');
        }

        if ($dto->phone) {
            $existing = $this->customerRepository->findByPhone($dto->phone, $shop->id);

            if ($existing) {
                throw new \RuntimeException('Customer with this phone number already exists.');
            }
        }

        return $this->customerRepository->create([
            'shop_id' => $shop->id,
            'name' => $dto->name,
            'phone' => $dto->phone,
            'email' => $dto->email,
            'address' => $dto->address,
        ]);
    }

    public function getCustomerWithCreditHistory(User $user, string $uuid): ?Customer
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            return null;
        }

        return $this->customerRepository->findByUuidWithBills($uuid, $shop->id);
    }

    public function updateCustomer(User $user, string $uuid, StoreCustomerDTO $dto): Customer
    {
        $shop = $this->shopRepository->findByUser($user);

        if (! $shop) {
            throw new \RuntimeException('Shop not found. Please setup your shop first.');
        }

        $customer = $this->customerRepository->findByUuid($uuid, $shop->id);

        if (! $customer) {
            throw new \RuntimeException('Customer not found.');
        }

        $data = array_filter([
            'name' => $dto->name,
            'phone' => $dto->phone,
            'email' => $dto->email,
            'address' => $dto->address,
        ], fn ($value) => ! is_null($value));

        return $this->customerRepository->update($customer, $data);
    }
}
