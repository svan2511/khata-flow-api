<?php

namespace App\DTOs;

class StockInDTO
{
    public function __construct(
        public readonly string $productUuid,
        public readonly float $quantity,
        public readonly ?float $costPrice = null,
        public readonly ?string $notes = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            productUuid: $data['product_uuid'],
            quantity: (float) $data['quantity'],
            costPrice: isset($data['cost_price']) ? (float) $data['cost_price'] : null,
            notes: $data['notes'] ?? null,
        );
    }
}
