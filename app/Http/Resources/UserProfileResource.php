<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this),
            'shop' => $this->relationLoaded('shop') && $this->shop
                ? new ShopResource($this->shop)
                : null,
        ];
    }
}
