<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $bills = $this->whenLoaded('bills');

        $totalBilled = $bills->isNotEmpty()
            ? (float) $bills->sum('total')
            : 0;

        $totalPaid = $bills->isNotEmpty()
            ? (float) $bills->sum('paid_amount')
            : 0;

        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'total_credit' => (float) $this->total_credit,
            'credit_summary' => [
                'total_billed' => $totalBilled,
                'total_paid' => $totalPaid,
                'outstanding' => (float) $this->total_credit,
            ],
            'bills' => BillListResource::collection($bills),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
