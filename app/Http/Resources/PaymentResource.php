<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'amount' => (float) $this->amount,
            'payment_method' => $this->payment_method,
            'reference' => $this->reference,
            'notes' => $this->notes,
            'payment_date' => $this->payment_date?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
