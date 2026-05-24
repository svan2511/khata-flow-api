<?php

namespace App\DTOs;

class CustomerQuickDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $phone,
        public readonly ?string $email,
        public readonly ?string $address,
    ) {}

    public static function fromRequest(array $data): self
    {
        $phone = isset($data['phone'])
            ? preg_replace('/\D/', '', $data['phone'])
            : null;

        return new self(
            name: $data['name'],
            phone: $phone,
            email: $data['email'] ?? null,
            address: $data['address'] ?? null,
        );
    }
}
