<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'bill_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'discount_type',
        'discount_value',
        'discount',
        'gst_rate',
        'cgst',
        'sgst',
        'tax',
        'subtotal',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'gst_rate' => 'decimal:2',
            'cgst' => 'decimal:2',
            'sgst' => 'decimal:2',
            'tax' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (BillItem $item): void {
            if (empty($item->uuid)) {
                $item->uuid = (string) Str::uuid();
            }
        });
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
