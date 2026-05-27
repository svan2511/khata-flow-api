<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
/**
 * @return array<string, mixed>
 */
    public function toArray(Request $request): array
    {
        return [
            'today_sales' => (float) $this['today_sales'],
            'today_credit' => (float) $this['today_credit'],
            'today_cash' => (float) $this['today_cash'],
            'total_credit' => (float) $this['total_credit'],
            'low_stock_count' => (int) $this['low_stock_count'],
            'today_bills_count' => (int) $this['today_bills_count'],
            'today_bills' => BillListResource::collection($this['today_bills']),
            'credit_customers' => CustomerResource::collection($this['credit_customers']),
            'has_shop' => (bool) $this['has_shop'],
        ];
    }
}
