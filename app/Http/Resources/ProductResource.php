<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'description' => $this->description,
            'price' => (float) $this->price,
            'cost_price' => (float) $this->cost_price,
            'mrp' => (float) $this->mrp,
            'unit' => $this->unit,
            'stock_quantity' => (float) $this->stock_quantity,
            'low_stock_threshold' => (float) $this->low_stock_threshold,
            'is_low_stock' => $this->isLowStock(),
            'image' => $this->image,
            'category' => new ProductCategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
