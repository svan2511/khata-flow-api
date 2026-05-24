<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'shop_name' => $this->shop_name,
            'shop_slug' => $this->shop_slug,
            'owner_name' => $this->owner_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'pincode' => $this->pincode,
            'gstin' => $this->gstin,
            'logo' => $this->logo,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
