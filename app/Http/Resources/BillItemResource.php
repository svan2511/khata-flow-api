<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'product_id' => $this->product?->uuid,
            'product_name' => $this->product_name,
            'quantity' => (float) $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'discount_type' => $this->discount_type,
            'discount_value' => (float) $this->discount_value,
            'discount' => (float) $this->discount,
            'gst_rate' => (float) $this->gst_rate,
            'cgst' => (float) $this->cgst,
            'sgst' => (float) $this->sgst,
            'tax' => (float) $this->tax,
            'subtotal' => (float) $this->subtotal,
            'total' => (float) $this->total,
        ];
    }
}
