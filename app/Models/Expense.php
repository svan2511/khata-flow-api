<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'shop_id',
        'title',
        'amount',
        'category',
        'payment_method',
        'expense_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date:Y-m-d',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Expense $expense): void {
            if (empty($expense->uuid)) {
                $expense->uuid = (string) Str::uuid();
            }
        });
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function scopeByShop($query, int $shopId)
    {
        return $query->where('shop_id', $shopId);
    }

    public function scopeByDateRange($query, string $from, ?string $to = null)
    {
        $query->whereDate('expense_date', '>=', $from);

        if ($to) {
            $query->whereDate('expense_date', '<=', $to);
        }

        return $query;
    }
}
