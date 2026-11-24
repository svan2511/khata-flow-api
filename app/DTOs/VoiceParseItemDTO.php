<?php

namespace App\DTOs;

class VoiceParseItemDTO
{
    public function __construct(
        public readonly ?string $productUuid,
        public readonly string $name,
        public readonly float $price,
        public readonly float $quantity,
        public readonly string $unit,
        public readonly float $gstRate,
        public readonly float $confidence,
        public readonly bool $resolved,
        public readonly string $spokenName,
    ) {}

    public function toArray(): array
    {
        return [
            'product_uuid' => $this->productUuid,
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'gst_rate' => $this->gstRate,
            'confidence' => $this->confidence,
            'resolved' => $this->resolved,
            'spoken_name' => $this->spokenName,
        ];
    }
}
