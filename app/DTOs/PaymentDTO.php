<?php

namespace App\DTOs;

class PaymentDTO
{
    public function __construct(
        public readonly float $amount,
        public readonly string $paymentMethod,
        public readonly ?string $reference = null,
        public readonly ?string $notes = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            amount: (float) $data['amount'],
            paymentMethod: $data['payment_method'],
            reference: $data['reference'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }
}
