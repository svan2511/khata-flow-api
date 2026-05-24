<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'phone',
        'otp',
        'purpose',
        'is_used',
        'expires_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'is_used' => 'boolean',
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Otp $otp): void {
            if (empty($otp->uuid)) {
                $otp->uuid = (string) Str::uuid();
            }
        });
    }

    public function isValid(): bool
    {
        return ! $this->is_used && $this->expires_at->isFuture();
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->where('is_used', false)
            ->where('expires_at', '>', now());
    }

    public function scopeForPhone(Builder $query, string $phone, string $purpose): Builder
    {
        return $query->where('phone', $phone)
            ->where('purpose', $purpose);
    }
}
