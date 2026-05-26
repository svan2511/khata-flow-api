<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomRangeReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'start_date' => $this['start_date'],
            'end_date' => $this['end_date'],
            'total_sales' => (float) $this['total_sales'],
            'total_bills' => (int) $this['total_bills'],
            'average_bill_value' => (float) $this['average_bill_value'],
            'average_per_day' => (float) $this['average_per_day'],
            'total_paid' => (float) $this['total_paid'],
            'total_due' => (float) $this['total_due'],
            'total_credit' => (float) $this['total_credit'],
            'payment_breakdown' => $this['payment_breakdown'],
            'top_products' => collect($this['top_products'])->map(fn ($item) => [
                'product_name' => $item['product_name'],
                'total_quantity' => (float) $item['total_quantity'],
                'total_revenue' => (float) $item['total_revenue'],
                'unit' => $item['unit'],
            ]),
        ];
    }
}
