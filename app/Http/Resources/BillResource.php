<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $gstBreakup = $this->computeGstBreakup();

        return [
            'id' => $this->uuid,
            'bill_number' => $this->bill_number,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'items' => BillItemResource::collection($this->whenLoaded('items')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),

            'subtotal' => (float) $this->subtotal,
            'discount_type' => $this->discount_type,
            'discount_value' => (float) $this->discount_value,
            'discount' => (float) $this->discount,
            'tax' => (float) $this->tax,
            'total' => (float) $this->total,
            'paid_amount' => (float) $this->paid_amount,
            'due_amount' => (float) $this->due_amount,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,

            'gst_breakup' => $gstBreakup,
            'items_count' => $this->items->count(),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function computeGstBreakup(): array
    {
        if (! $this->relationLoaded('items')) {
            return [];
        }

        return $this->items
            ->groupBy('gst_rate')
            ->map(function ($items, $rate) {
                $taxableValue = $items->sum(fn ($item) => $item->subtotal - $item->discount);
                $cgst = $items->sum('cgst');
                $sgst = $items->sum('sgst');

                return [
                    'gst_rate' => (float) $rate,
                    'taxable_value' => round($taxableValue, 2),
                    'cgst' => round($cgst, 2),
                    'sgst' => round($sgst, 2),
                    'total_tax' => round($cgst + $sgst, 2),
                ];
            })
            ->values()
            ->toArray();
    }
}
