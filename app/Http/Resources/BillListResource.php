<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillListResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'bill_number' => $this->bill_number,
            'customer_name' => $this->customer?->name,
            'subtotal' => (float) $this->subtotal,
            'discount' => (float) $this->discount,
            'tax' => (float) $this->tax,
            'total' => (float) $this->total,
            'paid_amount' => (float) $this->paid_amount,
            'due_amount' => (float) $this->due_amount,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'items_count' => $this->items->count(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
