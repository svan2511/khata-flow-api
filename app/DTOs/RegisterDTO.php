<?php

namespace App\DTOs;

class RegisterDTO
{
    public function __construct(
        public readonly string $phone,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            phone: preg_replace('/\D/', '', $data['phone']),
        );
    }

    public function toArray(): array
    {
        return [
            'phone' => $this->phone,
        ];
    }
}
