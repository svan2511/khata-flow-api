<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'current_month' => [
                'month' => $this['current_month']['month'],
                'total_sales' => (float) $this['current_month']['total_sales'],
                'total_bills' => (int) $this['current_month']['total_bills'],
                'average_per_day' => (float) $this['current_month']['average_per_day'],
                'total_credit' => (float) ($this['current_month']['total_credit'] ?? 0),
                'payment_breakdown' => $this['current_month']['payment_breakdown'],
                'top_products' => collect($this['current_month']['top_products'])->map(fn ($item) => [
                    'product_name' => $item['product_name'],
                    'total_quantity' => (float) $item['total_quantity'],
                    'total_revenue' => (float) $item['total_revenue'],
                    'unit' => $item['unit'],
                ]),
            ],
            'previous_month' => [
                'month' => $this['previous_month']['month'],
                'total_sales' => (float) $this['previous_month']['total_sales'],
                'total_bills' => (int) $this['previous_month']['total_bills'],
                'average_per_day' => (float) $this['previous_month']['average_per_day'],
            ],
            'comparison' => [
                'sales_growth_percentage' => (float) $this['comparison']['sales_growth_percentage'],
                'bills_growth_percentage' => (float) $this['comparison']['bills_growth_percentage'],
            ],
        ];
    }
}
