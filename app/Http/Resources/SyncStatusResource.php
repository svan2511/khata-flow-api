<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SyncStatusResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'is_synced' => (bool) $this['is_synced'],
            'pending_count' => (int) $this['pending_count'],
            'last_synced_at' => $this['last_synced_at'],
        ];
    }
}
