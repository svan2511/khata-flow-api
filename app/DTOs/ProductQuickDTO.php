<?php

namespace App\DTOs;

class ProductQuickDTO
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
        public readonly string $unit,
        public readonly float $lowStockThreshold,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            price: (float) ($data['price'] ?? 0),
            unit: $data['unit'] ?? 'pcs',
            lowStockThreshold: (float) ($data['low_stock_threshold'] ?? 0),
        );
    }
}
