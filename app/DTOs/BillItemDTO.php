<?php

namespace App\DTOs;

class BillItemDTO
{
    public function __construct(
        public readonly ?string $productUuid,
        public readonly float $quantity,
        public readonly ?string $discountType,
        public readonly ?float $discountValue,
        public readonly ?float $gstRate,
        public readonly ?string $productName = null,
        public readonly ?float $unitPrice = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            productUuid: $data['product_uuid'] ?? null,
            quantity: (float) ($data['quantity'] ?? 1),
            discountType: $data['discount_type'] ?? null,
            discountValue: isset($data['discount_value']) ? (float) $data['discount_value'] : null,
            gstRate: isset($data['gst_rate']) ? (float) $data['gst_rate'] : null,
            productName: $data['product_name'] ?? null,
            unitPrice: isset($data['unit_price']) ? (float) $data['unit_price'] : null,
        );
    }

    public function isCustomItem(): bool
    {
        return $this->productUuid === null;
    }
}
