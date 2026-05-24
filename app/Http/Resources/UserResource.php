<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'phone_verified_at' => $this->phone_verified_at?->toIso8601String(),
            'has_shop' => $this->relationLoaded('shop') ? $this->shop !== null : $this->hasShopSetup(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
