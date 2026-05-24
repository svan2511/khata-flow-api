<?php

namespace App\DTOs;

class StoreCustomerDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $phone = null,
        public readonly ?string $email = null,
        public readonly ?string $address = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            phone: isset($data['phone']) ? preg_replace('/\D/', '', $data['phone']) : null,
            email: $data['email'] ?? null,
            address: $data['address'] ?? null,
        );
    }
}
