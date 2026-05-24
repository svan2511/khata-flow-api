<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'shop_id',
        'name',
        'phone',
        'email',
        'address',
        'total_credit',
    ];

    protected function casts(): array
    {
        return [
            'total_credit' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Customer $customer): void {
            if (empty($customer->uuid)) {
                $customer->uuid = (string) Str::uuid();
            }
        });
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
