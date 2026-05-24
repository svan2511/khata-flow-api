<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

class StoreProductDTO
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
        public readonly string $unit,
        public readonly ?string $productCategoryId = null,
        public readonly ?string $categoryName = null,
        public readonly ?string $sku = null,
        public readonly ?string $barcode = null,
        public readonly ?string $description = null,
        public readonly ?float $costPrice = null,
        public readonly ?float $mrp = null,
        public readonly ?float $stockQuantity = null,
        public readonly ?float $lowStockThreshold = null,
        public readonly ?UploadedFile $image = null,
        public readonly ?bool $isActive = true,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            price: (float) $data['price'],
            unit: $data['unit'],
            productCategoryId: $data['product_category_id'] ?? null,
            categoryName: $data['category_name'] ?? null,
            sku: $data['sku'] ?? null,
            barcode: $data['barcode'] ?? null,
            description: $data['description'] ?? null,
            costPrice: isset($data['cost_price']) ? (float) $data['cost_price'] : null,
            mrp: isset($data['mrp']) ? (float) $data['mrp'] : null,
            stockQuantity: isset($data['stock_quantity']) ? (float) $data['stock_quantity'] : null,
            lowStockThreshold: isset($data['low_stock_threshold']) ? (float) $data['low_stock_threshold'] : 0,
            image: $data['image'] ?? null,
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : true,
        );
    }

    public function hasImage(): bool
    {
        return ! is_null($this->image) && $this->image->isValid();
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'unit' => $this->unit,
            'product_category_id' => $this->productCategoryId,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'description' => $this->description,
            'cost_price' => $this->costPrice,
            'mrp' => $this->mrp,
            'stock_quantity' => $this->stockQuantity ?? 0,
            'low_stock_threshold' => $this->lowStockThreshold ?? 0,
            'is_active' => $this->isActive ?? true,
        ];
    }
}
